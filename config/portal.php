<?php

return [

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

        /**  Controls if users can make partial payments when paying an invoice */
        'partial-payments-allowed' => false,


        'braintree' => [

            /**
             * Controls if PayPal is shown as a payment option when checking out with Braintree.
             * You must have PayPal configured in your BrainTree account for this to work
             */
            'supports-paypal' => false,
        ]
    ]


];
