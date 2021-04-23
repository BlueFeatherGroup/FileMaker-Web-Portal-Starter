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

                        <!-- Payment Info -->
                        <div class="min-h-[14rem]">
                            <div class="my-4 text-gray-800 font-medium">Pay Invoice #{{ invoiceId }}</div>
                            <div class="my-4">Invoice Amount: {{formatCurrency(total)}}</div>

                            <div id="dropin-container"></div>
                            <jet-input-error :message="form.errors.payment_error" class="mt-2"/>
                            <jet-validation-errors class="mb-4" />

                        </div>

                    </template>

                    <template #actions>


                        <div class="w-full">
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
import JetValidationErrors from '@/Jetstream/ValidationErrors'
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

            // payment amount seems ok
            this.step++;

            this.form.processing = true;

            this.$nextTick(function () {

            });


        },
    },
    mounted() {

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
    },
};
</script>

<style scoped>

</style>
