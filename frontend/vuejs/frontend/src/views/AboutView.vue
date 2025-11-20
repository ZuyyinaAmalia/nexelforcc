<script setup>
import { api } from '../services/api'
import { ref, onMounted } from 'vue'

const message = ref('')

onMounted(async () => {
  const res = await api.get('/test')
  message.value = res.data
})
</script>

<template>
  <div>
    <h1>Data dari Backend:</h1>
    <p>{{ message }}</p>
  </div>
</template>
