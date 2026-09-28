<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import {Head, InertiaForm} from '@inertiajs/vue3';
import FormComponent from "@/pages/Location/FormComponent.vue";
import type {Location } from '@/types/app.ts';
import {store} from "@/routes/location";

function createLocation(form: InertiaForm<Location>): void
{
    form.post(store().url)
}
</script>

<template>
    <Layout>
        <Head title="Create" />
        <h2>Create Location</h2>
        <FormComponent @form:submit="createLocation" />
    </Layout>
</template>
