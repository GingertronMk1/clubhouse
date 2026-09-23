<script setup lang="ts">
import {useForm, usePage} from "@inertiajs/vue3";
  import {attempt} from "@/routes/login";
import {computed} from "vue";

  const data = useForm({
      email: "user_01a0cfc5-a207-72ff-83d0-6fc8922356de",
      password: "not-registered-user",
  });

  function login() {
      data.post(attempt().url)
  }

  const errors = computed(() => usePage().props.errors);
</script>

<template>
    {{ errors }}
    <div class="gap-2 *:flex *:flex-col">
        <label for="email">
            <span>Email</span>
            <span v-if="errors.email" v-text="errors.email" />
        <input type="email" name="email" id="email" placeholder="Email Address" v-model="data.email">
        </label>
        <label for="password">
            <span>Password</span>
            <input type="password" name="password" id="password" v-model="data.password">
        </label>
        <button @click="login()">Log In</button>
    </div>
</template>
