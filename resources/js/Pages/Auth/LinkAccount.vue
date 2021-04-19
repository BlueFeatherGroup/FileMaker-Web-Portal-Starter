<template>
    <app-layout>
        <div class="mt-10 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <jet-validation-errors class="mb-4"/>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <div class="flex justify-center my-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <div class="mt-5 mb-10">Your login is not currently linked to a client account. Please enter an invoice
                    number and invoice amount to link your account.
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <jet-label for="invoice" value="Invoice Number"/>
                        <jet-input id="invoice" type="text" class="mt-1 block w-full" v-model.number.trim="form.invoice"
                                   required autofocus/>
                    </div>

                    <div class="mt-4">
                        <jet-label for="amount" value="Amount"/>
                        <currency-input id="amount" type="text" class="mt-1 block w-full" v-model.number="form.amount"
                                        required/>
                    </div>


                    <div class="my-10 flex flex-col justify-center text-center">
                        <jet-button class="justify-center" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Link
                        </jet-button>
                    </div>
                </form>
            </div>
        </div>


    </app-layout>
</template>


<script>

import JetAuthenticationCard from '@/Jetstream/AuthenticationCard';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo';
import JetButton from '@/Jetstream/Button';
import JetInput from '@/Jetstream/Input';
import JetCheckbox from '@/Jetstream/Checkbox';
import JetLabel from '@/Jetstream/Label';
import JetValidationErrors from '@/Jetstream/ValidationErrors';
import ButtonLink from "@/Pages/Components/ButtonLink";
import CurrencyInput from "@/Pages/Components/CurrencyInput";
import AppLayout from "@/Layouts/AppLayout";

export default {
    name: "LinkAccount",
    components: {
        AppLayout,
        CurrencyInput,
        ButtonLink,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors
    },
    props: {
        status: String
    },
    data() {
        return {
            form: this.$inertia.form({
                invoice: null,
                amount: '',
            })
        };
    },
    methods: {
        submit() {
            // let parsed = parseInt(this.form.invoice) || null;
            // if (parsed) {
            //     this.form.invoice = parsed;
            // }
            // this.form.invoice = parseInt(this.form.invoice);
            this.form.post(this.route('user.link-account.store'));
        }
    }
};
</script>

<style scoped>

</style>
