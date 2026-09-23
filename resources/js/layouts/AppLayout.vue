<script setup lang="ts">

import AppHeader from "@/components/header/AppHeader.vue";
</script>

<template>
    <AppHeader />
    <div class="flex flex-col bg-blue-400 grow">
        <div class="flex flex-col container mx-auto bg-gray-50 grow p-2">
            <slot/>
        </div>
    </div>
    <footer class="py-2 flex flex-row">
        <div class="container mx-auto flex flex-row justify-between">
            <h6 class="text-lg">Footer</h6>
        </div>
    </footer>
</template>

<style scoped>

</style>
