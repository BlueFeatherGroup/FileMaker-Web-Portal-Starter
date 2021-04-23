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
                        return this.modelValue;
                } else {
                    // User is not modifying now. Format display value for user interface

                    // show blank if field is empty
                    if (this.modelValue === '') {
                        return '';
                    }

                    // otherwise display it nicely formatted
                    let modelValue = parseFloat(this.modelValue);
                    return "$ " + modelValue.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,");
                }
            },
            set: function (modifiedValue) {
                // Recalculate value after ignoring "$" and "," in user input
                let newValue = modifiedValue.replace(/[^\d\.]/g, "");

                // Handle the value if it's not empty
                if (newValue !== '') {
                    let decimalCount = this.countDecimals(parseFloat(newValue));
                    if (decimalCount > 2){
                        newValue = parseFloat(newValue).toFixed(2);
                    } else {
                        newValue = newValue.toString();
                    }
                }

                // Note: we cannot set this.value as it is a "prop". It needs to be passed to parent component
                // $emit the event so that parent component gets it

                this.$emit('update:modelValue', newValue);
            }
        }
    },
    methods: {
        countDecimals: function (value) {
            if (Math.floor(value) === value) return 0;
            return value.toString().split(".")[1].length || 0;
        }
    }
};
</script>

<style scoped>

</style>
