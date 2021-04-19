<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //

    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        $customer = $user->customer;

        $outInvoices = $customer->outstandingInvoices;

        $client = "test";
        $data = [
            'client' => $client,
            'outInvoices' => $outInvoices
        ];

        return Inertia::render('Dashboard/Dashboard', $data);
    }

    public function getPaidInvoices(Request $request)
    {

        $customerId = Auth::user()->customer_id;
        $invoices = Invoice::where('date_payment', '*')->where('customer_id', "=" . $customerId)->orderByDesc('Sent On')->paginate(5);
        return $invoices;
    }
}
