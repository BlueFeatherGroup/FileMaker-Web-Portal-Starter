<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice #{{invoice.id}}
            </h2>
        </template>

        <div class="my-10 print:my-0"></div>

        <div class="mx-auto max-w-3xl">

            <div class="justify-end flex my-5 gap-4 print:hidden mx-5 sm:mx-0">
                <jet-secondary-button onclick="window.print();return false;">Print</jet-secondary-button>
                <inertia-link v-if="!invoice.date_payment" :href="route('invoice.pay', invoice.id)">
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
                                Invoice: {{ invoice.id }}
                            </div>
                            <div class="">
                                Date: {{ invoice.Date }}
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
                    Total: {{ formatCurrency(invoice.Total) }}
                </div>

                <div class="">
                    <div>Bill To:</div>
                    <div class="mb-5 text-sm text-gray-500">
                        <div>{{ customer.Company }}</div>
                        <div>{{ customer.address_1 }}</div>
                        <div>{{ customer.City }}, {{ customer.State }} 30022</div>
                    </div>
                </div>


                <table class="table-auto min-w-full ">
                    <thead class="text-gray-500 text-xs">
                    <tr>
                        <td class="py-2">DESCRIPTION</td>
                        <td class="text-right pl-14">QTY</td>
                        <td class="text-right pl-14">UNIT PRICE</td>
                        <td class="text-right pl-10 ">AMOUNT</td>
                    </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200 text-sm">

                    <template v-for="(lineItem, index) in lineItems" :key="lineItem.id">
                        <tr>

                            <td class="pl-5 py-3">
                                {{ lineItem.Item }}
                            </td>
                            <td class="text-right text-gray-500">{{ lineItem.Qty }}</td>
                            <td class="text-right">{{ formatCurrency(lineItem.unit_price) }}</td>
                            <td class="text-right">{{ formatCurrency(lineItem.Amount) }}</td>

                        </tr>
                    </template>
                    </tbody>
                </table>
                <div class="text-right my-5 font-bold">
                    Total: {{ formatCurrency(invoice.Total) }}
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
        customer: Object,
        lastProject: null,
        lastDescription: null,
    }
};
</script>

<style scoped>

</style>
