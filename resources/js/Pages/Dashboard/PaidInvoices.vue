<template>
    <div class="flex flex-col">
        <div class="-my-2 sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block w-full sm:px-6 lg:px-8">

                <div class="text-lg mb-5 pl-5 font-light text-gray-600">Paid Invoices</div>
                <!-- Table Mobile -->
                <div class="grid gap-y-4 sm:hidden">
                    <div v-if="invoices.length === 0"
                         class="shadow bg-white h-16 flex items-center justify-center text-gray-500">
                        You have no paid invoices.
                    </div>

                    <div v-for="invoice in invoices"
                         class="shadow bg-white p-5 grid grid-cols-2"
                         v-on:click="clickRow(invoice.id)">

                        <div>
                            <div class="">
                                <inertia-link class="text-blue-500 hover:text-blue-600 underline text-sm font-medium"
                                              :href="route('invoice.detail', invoice.id)">#{{ invoice.id }}
                                </inertia-link>
                            </div>
                            <div class=" text-xs text-gray-500">
                                Date: {{ invoice.Date }}
                            </div>
                        </div>
                        <div class="">
                            <div class="text-right">
                                <div class="text-lg">{{ formatCurrency(invoice.Total) }}</div>
                            </div>
                        </div>
                        <div>
                            <div class=" text-sm text-gray-900 mt-4">
                                {{ invoice.Summary }}
                            </div>
                        </div>
                    </div>

                    <pagination class="mt-6" :links="links" :current-page="currentPage" v-on:clicked="clickPagination"/>

                </div>

                <!-- Table Desktop Table -->
                <div class="hidden sm:block -my-2 sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="w-full divide-y divide-gray-200 table-auto">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="pl-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col"
                                        class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Summary
                                    </th>
                                    <th scope="col"
                                        class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col"
                                        class="px-3 md:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Paid On
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="loading">
                                    <td colspan="5" class="text-center py-24 bg-gray-100">
                                        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
                                    </td>
                                </tr>
                                <tr v-if="invoices.length === 0 && !loading" class="shadow bg-white h-16 text-gray-500">
                                    <td colspan="5" class="text-center">You have no paid invoices.
                                    </td>
                                </tr>
                                <tr v-if="!loading" v-for="invoice in invoices"
                                    class="cursor-pointer"
                                    v-on:click="clickRow(invoice.id)">
                                    <td class="pl-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium">
                                                <inertia-link class="text-blue-500 underline"
                                                              :href="route('invoice.detail', invoice.id)">
                                                    {{ invoice.id }}
                                                </inertia-link>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="">{{ invoice.Date }}</div>
                                    </td>
                                    <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ invoice.Summary }}
                                    </td>
                                    <td class="px-3 md:px-6 py-4 whitespace-nowrap text-right">
                                        <div>{{ formatCurrency(invoice.Total) }}</div>
                                    </td>
                                    <td class="px-3 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <div class="">{{ invoice.Date }}</div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination class="mt-6" :links="links" :current-page="currentPage" v-on:clicked="clickPagination"/>

                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.lds-ring {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}
.lds-ring div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 64px;
    height: 64px;
    margin: 8px;
    border: 8px solid #2F98CC;
    border-radius: 50%;
    animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-color: #2F98CC transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
    animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
    animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
    animation-delay: -0.15s;
}
@keyframes lds-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}


</style>

<script>
import Pagination from "@/Pages/Components/Pagination";

export default {
    name: "PaidInvoices",
    components: {Pagination},
    props: {},
    remember: {
        data: ['currentPage', 'invoices', 'loading', 'links'],
        key: 'PaidInvoices'
},
    methods: {
        clickRow(id) {
            let url = route('invoice.detail', id);
            this.$inertia.get(url);
        },
        clickPagination(page) {
            this.getInvoices(page);
        },
        getInvoices(page) {
            this.loading = true;
            if (page === 'Next &raquo;'){
                page = this.currentPage +1;
            } else if(page === '&laquo; Previous'){
                page = this.currentPage -1;
            }
            this.currentPage = parseInt(page);
            axios.get(route('dashboard-paid-invoices'), {params: {page: page}})
                .then(response => {
                        if (response.data) {
                            this.invoices = response.data.data;
                            this.links = response.data.links;
                        }
                        this.loading = false;
                    }
                );

        }
    },
    async created() {
        // initialize on page 1
        if(this.loading){
            this.getInvoices(this.currentPage);
        }
    },
    data() {
        return {
            loading: true,
            invoices: Array,
            links: Array,
            currentPage: 1,
        };
    },
};
</script>

<style scoped>

</style>
