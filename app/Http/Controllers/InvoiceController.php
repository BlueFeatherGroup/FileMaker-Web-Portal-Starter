<?php

namespace App\Http\Controllers;

use App\Mail\PaymentAlert;
use App\Mail\Receipt;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use BlueFeather\EloquentFileMaker\Support\Facades\FM;
use Braintree\Gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class InvoiceController extends Controller
{
    public function get(string $id)
    {

        // find the invoice or return a 404
        $invoice = Invoice::where('id', $id)->firstOrFail();

        $customer_id = Auth::user()->customer_id;
        $customer = Customer::find($customer_id);

        $lineItems = $invoice->lineItems;

        $data = [
            'customer' => $customer,
            'invoice' => $invoice,
            'lineItems' => $lineItems,
            'hasTax' => config('portal.payments.has-sales-tax'),
            'company' => config('portal.company'),
        ];

        return Inertia::render('Invoice/InvoiceDetail', $data);
    }

    public function pay(string $id)
    {
        $gateway = config('portal.payments.gateway');

        // Configure the payment process with the selected gateway
        if ($gateway === 'stripe') {
            return $this->payStripe($id);
        } elseif ($gateway === 'braintree') {
            return $this->payBraintree($id);
        }
    }

    protected function payStripe(string $id)
    {

        $invoice = Invoice::where('id', $id)->where('paid_on', '=')->firstOrFail();

        $user = Auth::user();

        // Assemble most of the data
        // We may need to add a client secret if only full payments are allowed
        $data = [
            'invoiceId' => $id,
            'total' => $invoice->total,
            'publishableKey' => config('stripe.publishable_key', env('STRIPE_PUBLISHABLE_KEY')),
            'email' => $user->email,
            'name' => $user->name,
        ];

        if (config('portal.payments.partial-payments-allowed')) {
            // Partial payments are allowed
            return Inertia::render('Invoice/PayStripe', $data);
        } else {
            // Only full payments are allowed

            // Add the client secret in to the data since we won't need to update it
            $data['clientSecret'] = $this->getClientSecretForPayment($invoice, $invoice->total);

            return Inertia::render('Invoice/PayFullStripe', $data);
        }


    }

    protected function getClientSecretForPayment($invoice, $amount)
    {

        Stripe::setApiKey(config('stripe.secret_key', env('STRIPE_SECRET_KEY')));

        $user = Auth::user();
        $intent = PaymentIntent::create([
            // amount is multiplied by 100 since they don't use decimals
            'amount' => round($amount * 100, 0),
            'currency' => 'usd',
            // Verify your integration in this guide by including this parameter
            'metadata' => [
                'customerId' => $invoice->customer_id,
                'invoiceId' => $invoice->id,
                'email' => $user->email,
                'integration_check' => 'accept_a_payment'],
        ]);

        return $intent->client_secret;
    }

    protected function payBraintree(string $id)
    {
        /** @var User $user */
        $user = Auth::user();
        // Create the gateway for all transactions
        $gateway = $this->getBraintreeGateway();

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

        $invoice = Invoice::where('id', $id)->where('paid_on', '=')->firstOrFail();

        // pass $clientToken to the front-end
        $clientToken = $gateway->clientToken()->generate([
            "customerId" => $user->braintree_customer_id,
        ]);

        $data = [
            'invoiceId' => $invoice->id,
            'total' => $invoice->total,
            'clientToken' => $clientToken,
            'usePayPal' => config('portal.payments.braintree.supports-paypal')
        ];

        // return a different view depending on if partial payments are allowed

        if (config('portal.payments.partial-payments-allowed')) {
            return Inertia::render('Invoice/Pay', $data);
        } else {
            return Inertia::render('Invoice/PayFull', $data);
        }
    }

    public function submitPayment(Request $request, string $id)
    {
        // handle submissions differently for Stripe and Braintree
        if (config('portal.payments.gateway') === 'stripe') {
            return $this->submitPaymentStripe();
        } elseif (config('portal.payments.gateway') === 'braintree') {
            return $this->submitPaymentBraintree($request, $id);
        }

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


    protected function getBraintreeGateway()
    {
        return new Gateway([
            'environment' => config('braintree.environment'),
            'merchantId' => config('braintree.merchantId'),
            'publicKey' => config('braintree.publicKey'),
            'privateKey' => config('braintree.privateKey')
        ]);
    }

    public function stripeWebhook(Request $request)
    {

        // return a 404 if stripe isn't configured
        if (config('portal.payments.gateway') !== 'stripe') {
            abort(404);
        }

        // Set the Stripe API key
        Stripe::setApiKey(config('stripe.secret_key', env('STRIPE_SECRET_KEY')));

        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('stripe-signature');

        $endpoint_secret = "whsec_nQbPUcBkQTeK0CZLR3sf5seV6uQcsGLI";

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                $amount = $paymentIntent->amount / 100;
                $card = $paymentIntent->charges->data[0]->payment_method_details->card;
                $cardBrand = $card->brand;
                $cardLastFour = $card->last4;
                $email = $paymentIntent->charges->data[0]->billing_details->email;
                $transactionId = $paymentIntent->id;
                $invoiceId = $paymentIntent->metadata['invoiceId'];
                $customerId = $paymentIntent->metadata['customerId'];
                $customer = Customer::find($customerId);
                $this->recordPaymentAndSendReceipt($customer, $invoiceId, $email, $amount, $cardBrand . " x-" . $cardLastFour, $transactionId);
                break;
            case 'payment_method.attached':
//                $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
//                handlePaymentMethodAttached($paymentMethod);
                break;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response(null, 200);
    }

    protected function recordPaymentAndSendReceipt($customer, $invoiceId, $email, $paymentAmount, $paymentMethod, $transactionId)
    {
        // Prepare the data for the success page and receipt
        $date = Carbon::now()->format('M j, Y');


        // Send an email receipt
        Mail::to($email)->send(new Receipt(number_format($paymentAmount, 2), $paymentMethod, $transactionId, $date));


        // prepare data for the payment submission script in FileMaker
        $scriptParam = json_encode([
            'invoiceId' => $invoiceId,
            'amount' => floatval($paymentAmount),
            'transactionId' => $transactionId,
        ]);
        // run our payment submission script
        $fmResult = FM::connection('fm-invoices')->layout(config('portal.payments.payment-received-layout'))->performScript(config('portal.payments.payment-received-script'), $scriptParam);


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
            Mail::to(config('portal.payments.payment-alert-to'))
                ->send(new PaymentAlert(number_format($paymentAmount, 2), $transactionId, $customer->name, $email, $errorMessage));

            // Don't throw an exception since we emailed ourselves
        } else {
            // email us a successful payment alert
            Mail::to(config('portal.payments.payment-alert-to'))
                ->send(new PaymentAlert(number_format($paymentAmount, 2), $transactionId, $customer->name, $email));
        }
    }

    protected function submitPaymentStripe()
    {

        $data = [
            'email' => Auth::user()->email,
        ];
        return Inertia::render('Invoice/SuccessStripe', $data);

    }

    protected function submitPaymentBraintree(Request $request, $id)
    {
        // get the invoice so we can make sure the payment amount is less than the remaining balance
        $invoice = Invoice::findOrFail($id);

        if (config('portal.partial-payments-allowed')) {
            // Make sure payment amount is valid
            $request = $request->validate([
                'paymentAmount' => 'required|numeric|min:1|max:' . $invoice->Total,
                'nonce' => 'required',
            ]);

            $paymentAmount = $request['paymentAmount'];
        } else {
            // Only full payments, so we don't have a payment amount
            $request = $request->validate([
                'nonce' => 'required',
            ]);

            $paymentAmount = $invoice->Total;
        }


        // get the data from the request
        $nonce = $request['nonce'];

        // prepare the gateway
        $gateway = $this->getBraintreeGateway();

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
            $error = ValidationException::withMessages([
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

        /** @var User $user */
        $user = Auth::user();

        $this->recordPaymentAndSendReceipt($user->customer, $id, $user->email, $paymentAmount, $paymentMethod, $transactionId);


        $date = Carbon::now()->format('M j, Y');


        $data = [
            'paymentAmount' => floatval($paymentAmount),
            'paymentMethod' => $paymentMethod,
            'paymentDate' => $date,
            'transactionId' => $transactionId,
        ];

        return Inertia::render('Invoice/Success', $data);
    }

    public function getStripeClientSecret(Request $request, $id)
    {
        // return a 404 if stripe isn't configured
        if (config('portal.payments.gateway') !== 'stripe') {
            abort(404);
        }

        $invoice = Invoice::findOrFail($id);

        $amount = $request->amount;
        $secret = $this->getClientSecretForPayment($invoice, $amount);

        $data = [
            'clientSecret' => $secret,
        ];

        return response()->json($data);
    }
}
