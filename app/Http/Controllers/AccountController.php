<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Scopes\BelongsToUserClientScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AccountController extends Controller
{

    public function showLinkAccount(){

        // check if they're already linked
        if (Auth::user()->customer_id){
            // user is already linked to a client
            // redirect them to the dashboard
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/LinkAccount');
    }

    public function storeLinkAccount(Request $request)
    {

        $validated = $request->validate([
            'invoice' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
        ]);

        // find an invoice by number and amount
        $invoice = Invoice::withoutGlobalScope(BelongsToUserClientScope::class)->where('id', "=" . $validated['invoice'])->where('total', $validated['amount'])->first();


        if (!$invoice) {
            // We couldn't find the invoice
            // Return an error

            $error = \Illuminate\Validation\ValidationException::withMessages([
                'link_error' => 'We were unable to find that invoice. Please try again.',
            ]);
            throw $error;
        }

        // we found an invoice
        $user = Auth::user();
        $user->customer_id = $invoice->customer_id;
        $user->save();


        // Account is linked. Put them on the dashboard.
        return Redirect::route('dashboard');
    }
}
