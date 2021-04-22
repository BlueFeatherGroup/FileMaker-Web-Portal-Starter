<template>
    <app-layout>
        <div class="max-w-xl mx-auto py-10 sm:px-6 lg:px-8">
            <card class="">
                <div class="success-animation my-5">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <div class="text-center my-10 text-lg">Thank you for your payment!</div>
                <div class="text-center">An email receipt has been sent to you at {{email}}.</div>

                <div class="text-center my-10">
                    <ButtonLink :href="route('dashboard')">Back to Dashboard</ButtonLink>
                </div>
            </card>
        </div>
    </app-layout>

</template>

<script>
import Card from "@/Pages/Components/Card";
import AppLayout from "@/Layouts/AppLayout";
import ButtonLink from "@/Pages/Components/ButtonLink";

export default {
    name: "Success",
    components: {ButtonLink, AppLayout, Card},
    props: {
        email: String,
    }
};
</script>

<style scoped>
.checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position: relative;
    top: 5px;
    right: 5px;
    margin: 0 auto;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4bb71b;
    fill: #fff;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;

}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }

    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0px 0px 0px 30px #4bb71b;
    }
}

</style>
