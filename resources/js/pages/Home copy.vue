
<template>
    <!-- <div>WELCOME TO HOME VUE</div> -->

    <h1>Name: {{ name }}</h1>
    <h1>Original Count: {{ count }}</h1>
    <h1>Double Count: {{ doubleCount }}</h1>

    <v-btn @click="increment">Count++</v-btn>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useSampleStore } from '../mainStore.js';

export default {
    data() {
        return {

        }
    },
    computed: {
        ...mapState(useSampleStore, ['count', 'name', 'doubleCount'])
    },
    methods: {
        ...mapActions(useSampleStore, ['increment'])
    },
}

</script>