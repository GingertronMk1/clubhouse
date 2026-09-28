<script setup lang="ts">
import {store} from "@/routes/location";
import {InertiaForm, useForm, usePage} from "@inertiajs/vue3";
import type {Location} from "@/types/app.ts";
import {computed} from "vue";
const props = defineProps<{
    location?: Location
}>();

const NEW_LOCATION_ID = "new-location";

const form = useForm<Location>(props.location ?? {
    id: NEW_LOCATION_ID,
    name: "",
    description: "",
    latitude: 0.0,
    longitude: 0.0,
    links: [],
    address1: "",
    address2: "",
    address3: "",
    postcode: "",
    city: "",
    country: "",
})

function removeLink(linkIndex: number) {
    form.links.splice(linkIndex, 1)
}

const errors = computed(() => usePage().props.errors);

defineEmits<{
    (e: 'form:submit', value: InertiaForm<Location>): void
}>();
</script>

<template>
    <form class="gap-4 flex flex-col [&_label]:flex [&_label]:flex-col" @submit.prevent="$emit('form:submit', form)">
        <label for="name">
            <span>
                Name
                <span class="text-red-500" v-if="errors.name" v-text="errors.name" />
            </span>
            <input type="text" name="name" id="name" v-model="form.name" />
        </label>
        <label for="description">
            Description
            <textarea name="description" id="description" v-model="form.description" />
        </label>
        <section class="flex flex-col gap-2">
            <h2>Address</h2>
            <label for="address1">
                Address Line 1
                <input type="text" name="address1" id="address1" v-model="form.address1" />
            </label>
            <label for="address1">
                Address Line 2
                <input type="text" name="address2" id="address2" v-model="form.address2" />
            </label>
            <label for="address1">
                Address Line 3
                <input type="text" name="address3" id="address3" v-model="form.address3" />
            </label>
            <label for="address1">
                Postcode
                <input type="text" name="postcode" id="postcode" v-model="form.postcode" />
            </label>
            <label for="address1">
                City
                <input type="text" name="city" id="city" v-model="form.city" />
            </label>
            <label for="address1">
                Country
                <input type="text" name="country" id="country" v-model="form.country" />
            </label>

        </section>
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
        <h3>Links</h3>
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
        <input type="submit" :value="form.id === NEW_LOCATION_ID ? 'Create' : 'Update'" />
    </form>
</template>

<style scoped>

</style>
