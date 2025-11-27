<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <RouterView />
</template>

<style>
/* Hapus style bawaan jika ada yang mengganggu layout */
body {
  margin: 0;
  padding: 0;
  background-color: #f9fafb; /* Sesuai bg-gray-50 Tailwind */
}
</style>
