<script setup lang="ts">
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps<{
    accessor: string
}>();

const error = computed(() => usePage().props.errors[props.accessor])
</script>

<template>
    <span v-if="error" v-text="error" class="text-red-500" />
</template>

<style scoped>

</style>
