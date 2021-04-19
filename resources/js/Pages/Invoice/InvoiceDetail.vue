<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice #{{invoice.number}}
            </h2>
        </template>

        <div class="my-10 print:my-0"></div>

        <div class="mx-auto max-w-3xl">

            <div class="justify-end flex my-5 gap-4 print:hidden mx-5 sm:mx-0">
                <jet-secondary-button onclick="window.print();return false;">Print</jet-secondary-button>
                <inertia-link v-if="!invoice.paidOn" :href="route('invoice.pay', invoice.id)">
                    <jet-button>Pay Now</jet-button>
                </inertia-link>
            </div>


            <div
                class="shadow print:shadow-none overflow-hidden border-b print:border-none border-gray-200 sm:rounded-lg bg-white p-1 sm:p-5">


                <div class="h-24 items-center mb-8 mt-2 print:mt-0">
                    <img class="float-left h-24" src="/img/icon.svg">
                    <div class="pl-32">
                        <div class="text-4xl">Invoice</div>
                        <div class="float-right text-right text-sm text-gray-500">
                            <div class="">
                                Invoice: {{ invoice.number }}
                            </div>
                            <div class="">
                                Date: {{ invoice.date }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <div>Blue Feather Group, LLC</div>
                            <div>9960 Timberstone Rd</div>
                            <div>Johns Creek, GA 30022</div>
                        </div>
                    </div>
                </div>


                <div class="my-5 float-right">
                    Total (USD): {{ formatCurrency(invoice.total_c) }}
                </div>

                <div class="">
                    <div>Bill To:</div>
                    <div class="mb-5 text-sm text-gray-500">
                        <div>{{ client.name }}</div>
                        <div>{{ client.billing_street_combined_c }}</div>
                        <div>{{ client.billing_city }}, {{ client.billing_state }} 30022</div>
                    </div>
                </div>


                <table class="table-auto min-w-full ">
                    <thead class="text-gray-500 text-xs">
                    <tr>
                        <td class="py-2">DESCRIPTION</td>
                        <td class="text-right pl-14">DATE</td>
                        <td class="text-right pl-14">QTY</td>
                        <td class="text-right pl-10 ">AMOUNT</td>
                    </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200 text-sm">

                    <template v-for="(lineItem, index) in lineItems" :key="lineItem.id">
                        <tr v-if="index === 0 || lineItem.project !== lineItems[index - 1].project ">
                            <td colspan="4"
                                class="text-base text-gray-500 text-xs pt-6 border-b-2 border-solid border-blue-500">
                                <div>{{ lineItem.project }}</div>
                            </td>
                        </tr>
                        <tr>

                            <td class="pl-5 py-3">
                                {{ lineItem.description }}
                            </td>
                            <td class="text-right text-gray-500">{{ lineItem.date }}</td>
                            <td class="text-right text-gray-500">{{ lineItem.qty }}</td>
                            <td class="text-right">{{ formatCurrency(lineItem.amount) }}</td>
                        </tr>
                    </template>
                    </tbody>
                </table>
                <div class="text-right my-5 font-bold">
                    Total (USD): {{ formatCurrency(invoice.total_c) }}
                </div>
            </div>
            <div class="py-5 print:hidden"></div>
        </div>
    </app-layout>
</template>

<script>
import JetButton from "@/Jetstream/Button";
import AppLayout from "@/Layouts/AppLayout";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";

export default {
    name: "InvoiceDetail",
    components: {JetSecondaryButton, AppLayout, JetButton},
    props: {
        invoice: Object,
        lineItems: Array,
        client: Object,
        lastProject: null,
        lastDescription: null,
    }
};
</script>

<style scoped>

</style>
