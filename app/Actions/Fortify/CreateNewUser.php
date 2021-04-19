<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
        $emailMatchFields = config('portal.account-link.link-via-email-fields');
        $query = Customer::query();
        foreach ($emailMatchFields as $index => $matchField){
            if ($index === 0){
                // Use a where for the first query
                $query->where($matchField, "\"" . $email . "\"");
            } else{
                // use an orWhere for subsequent fields
                $query->orWhere($matchField, "\"" . $email . "\"");
            }

        }

        // run the find and get results
        $customer = $query->first();

        if ($customer === null) {
            // We couldn't match to a client
            if (!config('portal.account-link.allow-invoice-match')){
                // We don't allow matches on invoice, so we need to throw an exception if we couldn't match to a client.

                $error = ValidationException::withMessages([
                    'link_error' => 'We were unable to link your email to one of our customers. Please contact us for assistance.',
                ]);
                throw $error;
            } else{
                // We allow matching on invoice numbers, so the user can try that later.
                // set it to null and let the middleware handle them matching
                $customerId = null;
            }

        } else {
            $customerId = $customer->id;
        }

        return User::create([
            'name' => $input['name'],
            'email' => $email,
            'password' => Hash::make($input['password']),
            'customer_id' => $customerId,
        ]);
    }
}
