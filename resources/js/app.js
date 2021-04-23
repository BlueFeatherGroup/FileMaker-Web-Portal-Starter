require('./bootstrap');

// Import modules...
import {createApp, h} from 'vue';
import {App as InertiaApp, plugin as InertiaPlugin} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';


const el = document.getElementById('app');


createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => require(`./Pages/${name}`).default,
        }),
})
    .mixin({
        methods: {
            route,
            formatCurrency(value) {

                if (typeof value !== "number") {
                    let floatVal = parseFloat(value);

                    // we couldn't parse it, so return the original value
                    if (isNaN(floatVal)){
                        return value;
                    }

                    // use the floatVal as our new value
                    value = floatVal;
                }

                var formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2
                });
                return formatter.format(value);
            }
        }
    })
    .use(InertiaPlugin)
    .mount(el);

InertiaProgress.init({color: '#4B5563'});
