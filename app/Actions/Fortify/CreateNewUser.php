<?php

namespace App\Actions\Fortify;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();


        // Try to link the user to a client if their client ID is null
        $email = $input['email'];
        $client = Client::where('email_contact', "\"" . $email . "\"")->orWhere('email_billing', "\"" . $email . "\"")->orWhere('email_billing_cc', "\"" . $email . "\"")->orWhere('email_billing_cc2', "\"" . $email . "\"")->first();

        if ($client === null) {
            // We couldn't match to a client
            // set it to null and let the middleware handle them matching
            $clientId = null;
        } else {
            $clientId = $client->id;
        }


        return User::create([
            'name' => $input['name'],
            'email' => $email,
            'password' => Hash::make($input['password']),
            'client_id' => $clientId,
        ]);
    }
}
