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

        $client = $user->client;

        $outInvoices = $client->outInvoices;

        $client = "test";
        $data = [
            'client' => $client,
            'outInvoices' => $outInvoices
        ];

        return Inertia::render('Dashboard/Dashboard', $data);
    }

    public function getPaidInvoices(Request $request)
    {

        $clientId = Auth::user()->client_id;
        $invoices = Invoice::where('paidOn', '*')->where('client_id', "=" . $clientId)->orderByDesc('date')->paginate(5);
        return $invoices;
    }
}
