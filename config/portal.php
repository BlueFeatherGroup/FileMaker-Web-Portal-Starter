<?php

return [
    'company' => [
        'name' => 'Blue Feather',
        'address_1' => '9960 Timberstone Rd',
        'address_2' => null,
        'city' => 'Johns Creek',
        'state' => 'GA',
        'postal_code' => '30022'
    ],

    'account-link' => [

        /**
         * Toggles if users should be allowed to link their account to a customer via invoice.
         * If True, users will be allowed to link their account to a customer using an invoice number and amount
         */
        'allow-invoice-match' => false,

        /**
         * An array of fields which can be used to try and map a registered user to a customer in the database
         */
        'link-via-email-fields' => [
            'office_email',
            'home_email'
        ]
    ],

    'payments' => [

        'has-sales-tax' => true,

        /**
         * Controls if users can make partial payments when paying an invoice
         */
        'partial-payments-allowed' => false,

        /**
         * Select which payment gateway to use
         * Either 'stripe' or 'braintree'
         */
        'gateway' => 'braintree',

        /**
         * Configure Stripe gateway information
         */
        'stripe' => [
            // nothing right now...
            'webhook-secret' => env('STRIPE_WEBHOOK_SECRET', '')
        ],

        /**
         * Configure Braintree gateway information
         */
        'braintree' => [

            /**
             * Controls if PayPal is shown as a payment option when checking out with Braintree.
             * You must have PayPal configured in your BrainTree account for this to work
             */
            'supports-paypal' => false,
        ],

        'payment-alert-to' => env('PAYMENT_ALERT_TO', 'me@mycompany.com'),


        /**
         * The name of the script which is triggered when a payment is received.
         * This script will receive the invoice id, payment amount, and transaction id as JSON parameters
         * This script should record the payment in your database
        */
        'payment-received-script' => 'PayInvoice',
        /**
         * The name of the layout to start on when calling the payment received script
         * A layout is required when calling scripts.
         * It is good to use a blank layout if possible and then have your script run from there.
        */
        'payment-received-layout' => 'blank'
    ]
];
