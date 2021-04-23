<template>
    <app-layout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pay Invoice
            </h2>
        </template>

        <div>
            <div class="max-w-xl mx-auto py-10 sm:px-6 lg:px-8">

                <form-card @submitted="submitPayment" class="">

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
                            <div id="dropin-container"></div>
                            <jet-input-error :message="form.errors.payment_error" class="mt-2"/>
                            <jet-validation-errors class="mb-4"/>

                        </div>

                    </template>

                    <template #actions>

                        <div v-if="step === 1">
                            <jet-button v-on:click="continueButton" :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing" :type="button">
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
import CurrencyInput from "@/Pages/Components/CurrencyInput";
import JetValidationErrors from '@/Jetstream/ValidationErrors';
import {useForm} from '@inertiajs/inertia-vue3';
import DropIn from 'braintree-web-drop-in';


export default {
    name: "Pay",
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
        outstandingBalance: Number,
        clientToken: String,
        usePayPal: Boolean,
    },
    setup() {
        const form = useForm({
            paymentAmount: null,
            nonce: null,
        });

        return {form};
    },
    data() {
        return {
            isProcessing: Boolean,
            dropinInstance: Object,
            paymentAmount: parseFloat(this.total),
            step: 1,
        };
    },
    methods: {
        submitPayment: function () {

            if (this.step === 1) {
                // we're still on step 1, so go to step 2 instead of continuing
                // The user probably pressed return;
                this.continueButton();
                return;
            }

            this.isProcessing = true;

            this.dropinInstance.requestPaymentMethod()
                .then((payload) => {
                    this.dropinCallback(payload);
                })
                .catch((error) => {
                    throw error;
                });
        },
        dropinCallback: function (payload) {
            this.form.paymentAmount = this.paymentAmount;
            this.form.nonce = payload.nonce;
            this.form.post(route('invoice.pay.submit', this.invoiceId));
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

            // payment amount seems ok
            this.step++;

            this.form.processing = true;

            this.$nextTick(function () {
                let options = {
                    card: {vault: {vaultCard: true, allowVaultCardOverride: true}},
                    vaultManager: true,
                    authorization: this.clientToken,
                };
                if (this.usePayPal) {
                    // PayPal is supported
                    options.paypal = {
                        flow: 'checkout',
                        amount: this.paymentAmount,
                        currency: 'USD',
                    };
                }
                options.container = document.getElementById('dropin-container');
                DropIn.create(options).then((dropinInstance) => {
                    this.dropinInstance = dropinInstance;
                    this.form.processing = false;
                }).catch((error) => {
                });
            });


        },
        backButton: function () {
            this.form.clearErrors();
            this.step--;
        }
    },
    mounted() {


    },
};
</script>

<style scoped>

</style>
