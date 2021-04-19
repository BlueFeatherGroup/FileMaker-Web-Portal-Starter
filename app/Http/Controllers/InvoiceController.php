<?php

namespace App\Http\Controllers;

use App\Mail\PaymentAlert;
use App\Mail\Receipt;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use BlueFeather\EloquentFileMaker\Support\Facades\FM;
use Braintree\Gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function get(string $id)
    {
        $clientId = Auth::user()->client_id;
        $client = Client::find($clientId);

        // find the invoice or return a 404
        $invoice = Invoice::where('id', $id)->firstOrFail();

        $unprocessedLines = Cache::remember('invoice-' . $invoice->id . '-lineitems', 600, function () use ($invoice) {
            return $invoice->lineItems()->orderBy('project')->orderBy('description')->orderBy('date')->get();
        });

        // combine lineitems with the same description so we have fewer repeated lines
        $lineItems = [];
        $lastDescription = null;
        $lastProject = null;
        foreach ($unprocessedLines as $eachLine) {
            // make a new line item if the description is different from the last item
            $thisLine = [
                'description' => $eachLine->description,
                'date' => $eachLine->date,
                'qty' => $eachLine->qty,
                'amount' => $eachLine->amount_c,
                'project' => $eachLine->project,
            ];

            if ($eachLine->description === $lastDescription && $eachLine->project === $lastProject) {
                // This line has the same project and description as the last line. We should combine the totals and update the existing line

                // Keep track of the size of the current array so we know where to update
                $index = sizeof($lineItems) - 1;

                $thisLine['qty'] = round($eachLine->qty + $lineItems[$index]['qty'], 2);
                $thisLine['amount'] = round($eachLine->amount_c + $lineItems[$index]['amount'], 2);
                $lineItems[$index] = $thisLine;
            } else {
                // This is a new project or new description, so we need to add a new line
                array_push($lineItems, $thisLine);
            }

            // update our lastProject and lastDescription to keep track of if we should add
            $lastProject = $eachLine->project;
            $lastDescription = $eachLine->description;

        }

        $data = [
            'client' => $client,
            'invoice' => $invoice,
            'lineItems' => $lineItems,
        ];

        return Inertia::render('Invoice/InvoiceDetail', $data);
    }

    public function pay(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        // Create the gateway for all transactions
        $gateway = $this->getGateway();

        if ($user->braintree_customer_id == null) {
            // There's no braintree ID, so we should create this person as a customer
            $result = $gateway->customer()->create([
                'email' => $user->email,
            ]);

            if (!$result->success) {
                // There was an error creating the customer
                // TODO handle it
                return;
            };

            // Store the customer ID
            $customerId = $result->customer->id;
            $user->braintree_customer_id = $customerId;
            $user->save();
        }

        $invoice = Invoice::where('id', $id)->where('remainingBalance_c', '>0')->firstOrFail();

        // pass $clientToken to the front-end
        $clientToken = $gateway->clientToken()->generate([
            "customerId" => $user->braintree_customer_id,
        ]);

        $data = [
            'invoiceNumber' => $invoice->number,
            'invoiceId' => $invoice->id,
            'total' => $invoice->total_c,
            'outstandingBalance' => $invoice->remainingBalance_c,
            'clientToken' => $clientToken
        ];
        return Inertia::render('Invoice/Pay', $data);
    }

    public function submitPayment(Request $request, string $id)
    {
        // get the invoice so we can make sure the payment amount is less than the remaining balance
        $invoice = Invoice::findOrFail($id);
        $balance = $invoice->remainingBalance_c;

        // Do basic validation
        $request = $request->validate([
            'paymentAmount' => 'required|numeric|min:1|max:' . $balance,
            'nonce' => 'required',
        ]);

        // get the data from the request
        $nonce = $request['nonce'];
        $paymentAmount = $request['paymentAmount'];

        // prepare the gateway
        $gateway = $this->getGateway();

        // Actually charge the card
        $result = $gateway->transaction()->sale([
            'amount' => $paymentAmount,
            'paymentMethodNonce' => $nonce,
            //'deviceData' => '',
            'options' => [
                'submitForSettlement' => True
            ]
        ]);


        if (!$result->success) {
            // There was an error processing
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'payment_error' => ['Error submitting payment: ' . $result->message],
            ]);
            throw $error;
        }

        // Pull important information from the transaction
        $transaction = $result->transaction;
        $paymentAmount = $transaction->amount;
        $paymentType = $transaction->paymentInstrumentType;
        $transactionId = $transaction->id;

        // Figure out the payment method text based on how they paid
        // Card or Paypal
        $paymentMethod = null;
        switch ($paymentType) {
            case ("credit_card"):
            {
                $paymentMethod = $transaction->creditCardDetails->cardType . " x-" . $transaction->creditCardDetails->last4;
                break;
            }
            case ("paypal_account"):
            {
                $paymentMethod = "PayPal - " . $transaction->paypal['payerEmail'];
                break;
            }
        }


        // Prepare the data for the success page and receipt
        $date = Carbon::now()->format('M j, Y');
        $data = [
            'paymentAmount' => floatval($paymentAmount),
            'paymentMethod' => $paymentMethod,
            'paymentDate' => $date,
            'transactionId' => $transactionId,
        ];

        // Get the client and user data so we can email ourselves shortly
        /** @var User $user */
        $user = Auth::user();
        $client = $user->client;


        // Send an email receipt
        Mail::to($user->email)->send(new Receipt(number_format($paymentAmount, 2), $paymentMethod, $transactionId, $date));


        // prepare data for the payment submission script in FileMaker
        $scriptParam = json_encode([
            'invoiceId' => $id,
            'amount' => floatval($paymentAmount),
            'transactionId' => $transactionId,
        ]);
        // run our payment submission script
        $fmResult = FM::connection('fm-invoices')->layout('glob')->performScript('PayInvoice', $scriptParam);


        // Check the results from the script
        $fmResult = json_decode($fmResult['response']['scriptResult']);
        if (!$fmResult->success) {
            // There was an error processing the payment
            $error = $fmResult->error;

            $errorMessage = <<<message
<div style="color:red; font-size: 20px">Error submitting payment to FileMaker:</div>
$error
message;


            // email us an error alert
            Mail::to(config('mail.to.payment-alert'))
                ->send(new PaymentAlert(number_format($paymentAmount, 2), $transactionId, $client->name, $user->email, $errorMessage));

            // Don't throw an exception since we emailed ourselves
        } else {
            // email us a successful payment alert
            Mail::to(config('mail.to.payment-alert'))
                ->send(new PaymentAlert(number_format($paymentAmount, 2), $transactionId, $client->name, $user->email));
        }

        return Inertia::render('Invoice/Success', $data);
    }


    public function success()
    {

        $paymentAmount = 100;
        $paymentMethod = "Visa x-1234";
        $transactionId = "asdfkhds";

        $date = Carbon::now()->format('M j, Y');
        $data = [
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'paymentDate' => $date,
            'transactionId' => $transactionId,
        ];

        //Mail::to(Auth::user()->email)->send(new Receipt(number_format($paymentAmount, 2), $paymentMethod, $transactionId, $date));

        return Inertia::render('Invoice/Success', $data);

    }


    protected function getGateway()
    {
        return new Gateway([
            'environment' => config('braintree.environment'),
            'merchantId' => config('braintree.merchantId'),
            'publicKey' => config('braintree.publicKey'),
            'privateKey' => config('braintree.privateKey')
        ]);
    }
}