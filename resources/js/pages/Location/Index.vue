<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import { Head } from '@inertiajs/vue3';
import type { Location } from '@/types/app.ts';

defineProps<{
    locations: Location[];
}>();
</script>

<template>
    <Layout>
        <Head title="Index" />
        <div class="flex flex-col gap-2">
            <section v-for="location in locations" :key="location.id">
                <p v-for="(val, index) in location" :key="index">
                    {{ index }}: {{ val }}
                </p>
            </section>
        </div>
    </Layout>
</template>
