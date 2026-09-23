<script setup lang="ts">
  import {useForm} from "@inertiajs/vue3";
  import {attempt} from "@/routes/login";

  const data = useForm({
      email: "",
      password: "",
  });

  function login() {
      data.post(attempt().url)
  }
</script>

<template>
    <div class="flex flex-col">
        <input type="email" name="email" id="email" v-model="data.email">
        <input type="password" name="password" id="password" v-model="data.password">
        <button @click="login()">Log In</button>
    </div>
</template>
