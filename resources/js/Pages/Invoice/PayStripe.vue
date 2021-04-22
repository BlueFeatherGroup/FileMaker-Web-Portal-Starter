<template>
    <app-layout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pay Invoice
            </h2>
        </template>

        <div>
            <div class="max-w-xl mx-auto py-10 sm:px-6 lg:px-8">

                <form-card @submitted="submitPayment" class="" id="payment-form">

                    <template #form>

                        <div v-if="step === 1">
                            <div class="mt-4 text-gray-800 font-medium">Pay Invoice #{{ invoiceId }}</div>
                            <div class="mt-4">Invoice Amount: {{ formatCurrency(total) }}</div>

                            <!-- Payment Amount -->
                            <div class="mt-6">
                                <jet-label for="paymentAmount" value="PaymentAmount"/>
                                <currency-input
                                    id="paymentAmount"
                                    class="mt-1 block w-full"
                                    v-model="paymentAmount"
                                    type="text"
                                />
                                <jet-input-error :message="form.errors.paymentAmount" class="mt-2"/>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div v-if="step === 2" class="min-h-[14rem]">
                            <div class="my-4 text-gray-800 font-medium">Pay Invoice #{{ invoiceId }}</div>
                            <div class="my-4">Invoice Amount: {{ formatCurrency(total) }}</div>

                            <jet-input-error :message="form.errors.payment_error" class="mt-2"/>
                            <jet-validation-errors class="mb-4"/>

                            <div class="mt-10">Card Information</div>
                            <div
                                class="px-3 text-sm py-2 border border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
                                <div id="card-element">
                                    <!-- Elements will create input elements here -->
                                </div>
                            </div>


                            <!-- We'll put the error messages in this element -->
                            <div v-show="cardErrors" role="alert" class="text-red-500 my-2">{{ cardErrors }}</div>

                        </div>
                    </template>

                    <template #actions>


                        <div v-if="step === 1">
                            <jet-button v-on:click.prevent="continueButton" :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing">
                                Continue to Payment
                            </jet-button>
                        </div>

                        <div v-if="step === 2" class="w-full">
                            <jet-secondary-button class="float-left" v-on:click="backButton"
                                                  :class="{ 'opacity-25': form.processing }"
                                                  :disabled="form.processing">
                                Back
                            </jet-secondary-button>
                            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Pay {{ formatCurrency(paymentAmount) }}
                            </jet-button>
                        </div>

                    </template>

                </form-card>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import FormCard from "@/Pages/Components/FormCard";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetInput from "@/Jetstream/Input";
import JetButton from '@/Jetstream/Button';
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetLabel from '@/Jetstream/Label';
import JetInputError from '@/Jetstream/InputError';
import JetValidationErrors from '@/Jetstream/ValidationErrors';
import {useForm} from '@inertiajs/inertia-vue3';
import {loadStripe} from '@stripe/stripe-js';
import CurrencyInput from "@/Pages/Components/CurrencyInput";
import {nextTick} from "vue";


export default {
    name: "PayStripe",
    components: {
        CurrencyInput,
        FormCard,
        AppLayout,
        JetActionMessage,
        JetButton,
        JetInput,
        JetSecondaryButton,
        JetLabel,
        JetInputError,
        JetValidationErrors
    },
    props: {
        invoiceId: String,
        total: Number,
        publishableKey: String,
        name: String,
        email: String,
    },
    setup() {
        const form = useForm({});

        return {form};
    },
    data() {
        return {
            paymentAmount: parseFloat(this.total),
            stripe: Object,
            elements: Object,
            cardErrors: null,
            card: Object,
            step: 1,
            clientSecret: String,
        };
    },
    methods: {
        submitPayment: async function () {

            if (this.step === 1) {
                // we're still on step 1, so go to step 2 instead of continuing
                // The user probably pressed return;
                this.continueButton();
                return;
            }


            this.form.processing = true;

            if (!this.stripe || !this.elements) {
                // Stripe.js has not yet loaded.
                // Make sure to disable form submission until Stripe.js has loaded.
                return;
            }

            const result = await this.stripe.confirmCardPayment(this.clientSecret, {
                payment_method: {
                    card: this.card,
                    billing_details: {
                        name: this.name,
                        email: this.email,
                    },
                }
            });

            if (result.error) {
                // Show error to your customer (e.g., insufficient funds)
                this.cardErrors = result.error.message;
                console.log(result.error.message);
                this.form.processing = false;
            } else {
                // The payment has been processed!
                if (result.paymentIntent.status === 'succeeded') {
                    // Show a success message to your customer
                    // There's a risk of the customer closing the window before callback
                    // execution. Set up a webhook or plugin to listen for the
                    // payment_intent.succeeded event that handles any business critical
                    // post-payment actions.

                    this.form.post(route('invoice.pay.submit', this.invoiceId));

                }
            }


        },
        continueButton: function () {

            // Check for payment amount errors
            if (this.paymentAmount < 1) {
                this.form.errors.paymentAmount = "Payment amount must at least $1";
                return false;
            }

            //Make sure they're not submitting over the maximum amount of the invoice
            if (this.paymentAmount > this.total) {
                this.form.errors.paymentAmount = "Payment amount must not exceed invoice amount.";
                return false;
            }

            // get a client secret for the payment
            axios.post(route('invoice.pay.stripe.get-client-secret', this.invoiceId), {amount: this.paymentAmount})
                .then(async response => {

                    // get the clientSecret from the response
                    if (response.data) {
                        this.clientSecret = response.data.clientSecret;
                    }


                    this.step++;

                    // wait for the dom to load
                    await nextTick()
                    // load the stripe elements
                    this.stripe = await loadStripe(this.publishableKey);

                    this.elements = this.stripe.elements();
                    var style = {
                        base: {
                            color: "#000",
                            fontSize: "16px",
                            fontFamily: 'Nunito, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                        }
                    };

                    this.card = this.elements.create("card", {style: style});
                    this.card.mount("#card-element");

                    this.card.on('change', ({error}) => {
                        if (error) {
                            this.cardErrors = error.message;
                        } else {
                            this.cardErrors = null;
                        }
                    });

                    // The component starts in the processing state, so disable it once we're done loading.
                    this.form.processing = false;
                    }
                );

        },
        backButton: function () {
            this.form.clearErrors();
            this.step--;
        }
    },
    async mounted() {


    },
};
</script>

<style scoped>

</style>
