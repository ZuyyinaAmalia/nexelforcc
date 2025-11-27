<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const errorMessage = ref('');
const isLoading = ref(false);

const handleLogin = async () => {
  // Reset state
  errorMessage.value = '';
  isLoading.value = true;

  try {
    // 1. Tembak API Login
    // Ganti URL sesuai alamat server Laravel kamu
    const response = await axios.post('http://127.0.0.1:8000/api/login-penjual', {
      email: email.value,
      password: password.value
    });

    // 2. Ambil token & user dari response
    const { access_token, user } = response.data;

    // 3. Simpan Token di LocalStorage
    localStorage.setItem('token', access_token);
    localStorage.setItem('user', JSON.stringify(user));

    // 4. Redirect ke Dashboard (Buat halaman ini nanti)
    console.log("Login Sukses:", user);
    router.push('/dashboard-penjual'); 
    alert("Login Berhasil! Token tersimpan.");

  } catch (error: any) {
    if (error.response && error.response.status === 401) {
      errorMessage.value = "Email atau password salah.";
    } else {
      errorMessage.value = "Terjadi kesalahan pada server.";
      console.error(error);
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-lg rounded-2xl sm:px-10">
        
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-purple-700 mb-3">Login</h2>
          <p class="mt-2 text-sm text-gray-500">
            Silakan masukkan kredensial Anda untuk melanjutkan
          </p>
        </div>

        <form class="space-y-6" @submit.prevent="handleLogin">
          
          <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3">
            {{ errorMessage }}
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input 
                v-model="email"
                id="email" 
                name="email" 
                type="email" 
                required 
                class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-3 py-3 sm:text-sm border-gray-300 rounded-lg border" 
                placeholder="nama@email.com"
              >
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input 
                v-model="password"
                :type="showPassword ? 'text' : 'password'" 
                id="password" 
                name="password" 
                required 
                class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-3 pr-10 py-3 sm:text-sm border-gray-300 rounded-lg border" 
                placeholder="••••••••"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" @click="showPassword = !showPassword">
                 <svg v-if="!showPassword" class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.064 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                  </svg>
              </div>
            </div>
          </div>

          <div>
            <button 
                type="submit" 
                :disabled="isLoading"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading">Memproses...</span>
              <span v-else>Masuk</span>
            </button>
          </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Belum punya akun? 
              <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
                Daftar di sini
              </router-link>
            </p>
        </div>

      </div>
    </div>
  </div>
</template>