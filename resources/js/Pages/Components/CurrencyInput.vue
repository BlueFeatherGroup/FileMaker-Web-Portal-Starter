<template>
    <input type="text" v-model="displayValue" @blur="isInputActive = false" @focus="isInputActive = true"
           class="border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
</template>

<script>
export default {
    name: "CurrencyInput",
    props: ['modelValue'],
    data() {
        return {
            isInputActive: false
        };
    },
    emits: ['update:modelValue'],
    computed: {
        displayValue: {
            get: function () {
                if (this.isInputActive) {
                    // Cursor is inside the input field. unformat display value for user
                    // check if we should set the value to have decimal places or not
                    if (this.modelValue === '') {
                        return this.modelValue;
                    }
                    if (this.modelValue % 1 > 0) {
                        // there is a decimal value
                        return this.modelValue.toFixed(2);
                    } else {
                        // There is no decimal value
                        return this.modelValue.toString();
                    }
                } else {
                    // User is not modifying now. Format display value for user interface

                    // show blank if field is empty
                    if (this.modelValue === '') {
                        return '';
                    }

                    // otherwise display it nicely formatted
                    return "$ " + this.modelValue.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
                }
            },
            set: function (modifiedValue) {
                // Recalculate value after ignoring "$" and "," in user input
                let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""));
                // Ensure that it is not NaN
                if (isNaN(newValue)) {
                    newValue = '';
                }
                // Note: we cannot set this.value as it is a "prop". It needs to be passed to parent component
                // $emit the event so that parent component gets it
                this.$emit('update:modelValue', newValue);
            }
        }
    },
    methods: {
        countDecimals: function () {
            if (Math.floor(this.valueOf()) === this.valueOf()) return 0;
            return this.toString().split(".")[1].length || 0;
        }
    }
};
</script>

<style scoped>

</style>
