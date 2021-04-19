<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div  class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="my-5"></div>
                <outstanding-invoices :invoices="outInvoices"/>
            </div>
        </div>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="my-5"></div>
                <paid-invoices :invoices="outInvoices"/>
            </div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Welcome from '@/Jetstream/Welcome';
import OutstandingInvoices from "@/Pages/Dashboard/OutstandingInvoices";
import PaidInvoices from "@/Pages/Dashboard/PaidInvoices";
import { useRemember } from '@inertiajs/inertia-vue3'

export default {
    name: "Dashboard",
    components: {
        PaidInvoices,
        OutstandingInvoices,
        AppLayout,
        Welcome,
    },
    props: {
        outInvoices: Object
    },
    remember:{data: ['tab'], key: 'Dashboard'},
    data() {
        return {
            tab: 1
        };

    },
    methods: {

    }
};
</script>
