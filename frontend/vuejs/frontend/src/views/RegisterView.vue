<script setup lang="ts">
import { ref } from 'vue';

// Daftar Provinsi sesuai Model/Migration
const provinsiList = [
    'Nanggroe Aceh Darussalam', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
    'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Bangka Belitung',
    'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
    'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
    'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
    'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
    'Maluku', 'Maluku Utara',
    'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
];

// State Form
const form = ref({
    email: '',
    password: '',
    namaPenjual: '',
    nik: '',
    noHp: '',
    namaToko: '',
    deskripsiToko: '',
    jalan: '',
    rt: '',
    rw: '',
    desa: '',
    kota: '',
    provinsi: ''
});

// State untuk File Upload
const fileFoto = ref<File | null>(null);
const fileKtp = ref<File | null>(null);

const handleFileChange = (e: Event, type: 'foto' | 'ktp') => {
    const target = e.target as HTMLInputElement;
    // Cek apakah files ada
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        // Gunakan operator '?? null' untuk mengubah undefined jadi null jika perlu
        if (type === 'foto') fileFoto.value = file ?? null;
        else fileKtp.value = file ?? null;
    }
};

const handleRegister = () => {
    // Logika kirim data (FormData karena ada upload file)
    const formData = new FormData();
    // Append semua text field
    Object.keys(form.value).forEach(key => {
        formData.append(key, form.value[key as keyof typeof form.value]);
    });
    // Append files
    if (fileFoto.value) formData.append('foto', fileFoto.value);
    if (fileKtp.value) formData.append('fotoKtp', fileKtp.value);

    console.log("Mengirim data registrasi...");
    // Nanti panggil API Backend di sini
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl"> <div class="bg-white py-8 px-4 shadow-lg rounded-2xl sm:px-10">
        <div class="mb-8 border-b pb-4">
            <h2 class="text-3xl font-bold text-gray-900">Registrasi Penjual</h2>
            <p class="mt-2 text-sm text-gray-500">Mulai jualan dengan mendaftarkan toko Anda.</p>
        </div>

        <form class="space-y-6" @submit.prevent="handleRegister">
            
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3">Informasi Akun</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input v-model="form.password" type="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input v-model="form.namaPenjual" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK (16 Digit)</label>
                            <input v-model="form.nik" type="text" maxlength="16" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input v-model="form.noHp" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Profil</label>
                            <input @change="handleFileChange($event, 'foto')" type="file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto KTP</label>
                            <input @change="handleFileChange($event, 'ktp')" type="file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Data Toko</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
                        <input v-model="form.namaToko" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Toko</label>
                        <textarea v-model="form.deskripsiToko" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Alamat Lengkap</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jalan</label>
                        <input v-model="form.jalan" type="text" placeholder="Jl. Mawar No. 12" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RT</label>
                            <input v-model="form.rt" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RW</label>
                            <input v-model="form.rw" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                            <input v-model="form.desa" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                            <input v-model="form.kota" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <select v-model="form.provinsi" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-md border">
                            <option value="" disabled>Pilih Provinsi</option>
                            <option v-for="p in provinsiList" :key="p" :value="p">{{ p }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition">
                    Daftar Sekarang
                </button>
            </div>
        </form>

         <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Sudah punya akun? 
              <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
                Masuk di sini
              </router-link>
            </p>
          </div>

      </div>
    </div>
  </div>
</template>