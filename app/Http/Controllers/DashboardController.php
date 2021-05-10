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

        $outInvoices = $customer->outstandingInvoices()->orderByDesc('date')->get();

        $client = "test";
        $data = [
            'client' => $client,
            'outInvoices' => $outInvoices
        ];

        return Inertia::render('Dashboard/Dashboard', $data);
    }

    public function getPaidInvoices(Request $request)
    {

        $invoices = Invoice::where('paid_on', '*')->orderByDesc('date')->paginate(5);
        return $invoices;
    }
}
