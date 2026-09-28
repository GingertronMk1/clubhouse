<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import {Head, InertiaForm} from '@inertiajs/vue3';
import FormComponent from "@/pages/Location/FormComponent.vue";
import type {Location} from '@/types/app.ts';
import {update} from "@/routes/location";

defineProps<{
    location: Location;
}>();

function updateLocation(form: InertiaForm<Location>) {
    form.post(update(form.id).url)
}
</script>

<template>
    <Layout>
        <Head title="Edit" />
        <FormComponent :location="location" @form:submit="updateLocation" />
    </Layout>
</template>
