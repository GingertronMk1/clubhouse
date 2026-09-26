<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import { store } from "@/routes/location"
import type {Location} from "@/types/app";

const form = useForm<Location>({
    id: "new-location",
    name: "",
    description: "",
    latitude: 0.0,
    longitude: 0.0,
    links: [],
})

function removeLink(linkIndex: number) {
    console.table(form.links);
    console.log(linkIndex);
    form.links.splice(linkIndex, 1)
}
</script>

<template>
    <Layout>
        <Head title="Create" />
        <h2>Create Location</h2>
        <form class="gap-4 [&_label]:flex [&_label]:flex-col" @submit="form.post(store().url)">
            <label for="name">
                Name
                <input type="text" name="name" id="name" v-model="form.name" />
            </label>
            <label for="description">
                Description
                <textarea name="description" id="description" v-model="form.description" />
            </label>
            <section class="flex flex-row gap-2">
                <label for="latitude">
                    Latitude
                    <input type="number" name="latitude" id="latitude" v-model="form.latitude" />
                </label>
                <label for="longitude">
                    Longitude
                    <input type="number" name="longitude" id="longitude" v-model="form.longitude" />
                </label>
            </section>
            <h4>Links</h4>
            <section class="flex flex-col gap-2">
                <section class="flex flex-row" v-for="linkIndex in (Object.keys(form.links) as unknown as number[])" :key="linkIndex">
                    <label for="title">
                        Link title
                        <input type="text" name="title" id="title" v-model="form.links[linkIndex].title" />
                    </label>
                    <label for="url">
                        Link URL
                        <input type="text" name="url" id="url" v-model="form.links[linkIndex].url" />
                    </label>
                    <button @click.prevent="removeLink(linkIndex)">Remove link</button>
                </section>

                <button @click.prevent="form.links.push({
                    title: `New Link ${Object.keys(form.links).length}`  ,
                    url: ''
                })">Add link</button>
            </section>
        </form>
    </Layout>
</template>
