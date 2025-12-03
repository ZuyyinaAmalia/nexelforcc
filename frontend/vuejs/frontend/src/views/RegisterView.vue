<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'; 
// Pastikan path import ini sesuai struktur folder Anda
import { penjualRegister, getErrorMessage } from '@/services/api';

const router = useRouter();
const isLoading = ref(false);

// --- API WILAYAH URL ---
const API_WILAYAH_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';

// --- STATE WILAYAH (Untuk Opsi Dropdown) ---
const provinces = ref<any[]>([]);
const regencies = ref<any[]>([]); // Kota/Kab
const districts = ref<any[]>([]); // Kecamatan
const villages = ref<any[]>([]);  // Desa/Kelurahan

// --- STATE SELECTED IDs (Digunakan untuk fetch API anak) ---
const selectedRegionIds = ref({
    provinsi: '',
    kota: '',
    kecamatan: '',
    desa: ''
});

// --- STATE FORM UTAMA (Yang dikirim ke Backend - Berisi NAMA daerah) ---
const form = ref({
    email: '', password: '', namaPenjual: '', nik: '', noHp: '',
    namaToko: '', deskripsiToko: '', jalan: '', rt: '', rw: '',
    provinsi: '', kota: '', kecamatan: '', desa: '' 
});

// --- STATE ERROR & FILE ---
const errors = ref<Record<string, string>>({});
const fileFoto = ref<File | null>(null);
const fileKtp = ref<File | null>(null);

// --- LOGIC API WILAYAH (CASCADING) ---

// 1. Fetch Provinsi (Saat Mounted)
const fetchProvinces = async () => {
    try {
        const res = await axios.get(`${API_WILAYAH_BASE}/provinces.json`);
        provinces.value = res.data;
    } catch (e) { console.error("Gagal ambil provinsi", e); }
};

// 2. Handle Ganti Provinsi
const handleProvinsiChange = async () => {
    // 1. Ambil Nama Provinsi berdasarkan ID yang dipilih
    const prov = provinces.value.find(p => p.id === selectedRegionIds.value.provinsi);
    form.value.provinsi = prov ? prov.name : '';
    
    // 2. Reset Anak (Kota, Kec, Desa)
    regencies.value = []; districts.value = []; villages.value = [];
    selectedRegionIds.value.kota = ''; 
    selectedRegionIds.value.kecamatan = ''; 
    selectedRegionIds.value.desa = '';
    form.value.kota = ''; form.value.kecamatan = ''; form.value.desa = '';

    // 3. Validasi & Fetch Kota
    validateField('provinsi');
    if (selectedRegionIds.value.provinsi) {
        try {
            const res = await axios.get(`${API_WILAYAH_BASE}/regencies/${selectedRegionIds.value.provinsi}.json`);
            regencies.value = res.data;
        } catch (e) { console.error(e); }
    }
};

// 3. Handle Ganti Kota
const handleKotaChange = async () => {
    const kota = regencies.value.find(r => r.id === selectedRegionIds.value.kota);
    form.value.kota = kota ? kota.name : '';

    // Reset Anak (Kec, Desa)
    districts.value = []; villages.value = [];
    selectedRegionIds.value.kecamatan = ''; 
    selectedRegionIds.value.desa = '';
    form.value.kecamatan = ''; form.value.desa = '';

    validateField('kota');
    if (selectedRegionIds.value.kota) {
        try {
            const res = await axios.get(`${API_WILAYAH_BASE}/districts/${selectedRegionIds.value.kota}.json`);
            districts.value = res.data;
        } catch (e) { console.error(e); }
    }
};

// 4. Handle Ganti Kecamatan
const handleKecamatanChange = async () => {
    const kec = districts.value.find(d => d.id === selectedRegionIds.value.kecamatan);
    form.value.kecamatan = kec ? kec.name : '';

    // Reset Anak (Desa)
    villages.value = [];
    selectedRegionIds.value.desa = '';
    form.value.desa = '';

    validateField('kecamatan');
    if (selectedRegionIds.value.kecamatan) {
        try {
            const res = await axios.get(`${API_WILAYAH_BASE}/villages/${selectedRegionIds.value.kecamatan}.json`);
            villages.value = res.data;
        } catch (e) { console.error(e); }
    }
};

// 5. Handle Ganti Desa
const handleDesaChange = () => {
    const desa = villages.value.find(v => v.id === selectedRegionIds.value.desa);
    form.value.desa = desa ? desa.name : '';
    validateField('desa');
};

onMounted(() => { fetchProvinces(); });

// --- VALIDASI & FILE HANDLER ---

const handleFileChange = (e: Event, type: 'foto' | 'ktp') => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        // FIX TS Error: Pastikan file diambil dengan aman
        const file = target.files[0];
        
        // Guard clause: jika file undefined, hentikan fungsi
        if (!file) return;

        if (type === 'foto') { 
            fileFoto.value = file; 
            errors.value.foto = ''; 
        } else { 
            fileKtp.value = file; 
            errors.value.fotoKtp = ''; 
        }
    }
};

const validateField = (field: string) => {
    const val = form.value[field as keyof typeof form.value];
    errors.value[field] = '';

    if (!val || val === '') { errors.value[field] = 'Wajib diisi'; return; }

    if (field === 'password') {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!regex.test(val as string)) errors.value.password = "Min 8 kar, Besar, kecil, angka & simbol.";
    }
    if (field === 'nik') {
        if (!/^\d+$/.test(val as string) || (val as string).length !== 16) errors.value.nik = "Harus angka 16 digit.";
    }
    if (field === 'noHp') {
        if (!(val as string).startsWith('08')) errors.value.noHp = "Harus diawali '08'.";
    }
    if (field === 'deskripsiToko') {
        if ((val as string).length < 20) errors.value.deskripsiToko = `Kurang panjang (${(val as string).length}/20)`;
    }
};

// --- SUBMIT REGISTER ---
const handleRegister = async () => {
    let isValid = true;
    
    // Validasi Text
    Object.keys(form.value).forEach(key => {
        validateField(key);
        if (errors.value[key]) isValid = false;
    });
    
    // Validasi Manual Dropdown (untuk memastikan terisi)
    if (!selectedRegionIds.value.provinsi) { errors.value.provinsi = 'Wajib pilih'; isValid = false; }
    if (!selectedRegionIds.value.kota) { errors.value.kota = 'Wajib pilih'; isValid = false; }
    if (!selectedRegionIds.value.kecamatan) { errors.value.kecamatan = 'Wajib pilih'; isValid = false; }
    if (!selectedRegionIds.value.desa) { errors.value.desa = 'Wajib pilih'; isValid = false; }

    if (!isValid) {
        alert("Mohon perbaiki isian yang berwarna merah.");
        return;
    }

    isLoading.value = true;
    const formData = new FormData();
    
    // Append Text
    Object.keys(form.value).forEach(key => {
        formData.append(key, form.value[key as keyof typeof form.value]);
    });
    // Append File
    if (fileFoto.value) formData.append('foto', fileFoto.value);
    if (fileKtp.value) formData.append('fotoKtp', fileKtp.value);

    try {
        await penjualRegister(formData);
        alert("Registrasi Berhasil! Silakan Login.");
        router.push('/login');
    } catch (error: any) {
        console.error("Register Error:", error);
        alert("Gagal: " + getErrorMessage(error));
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
      <div class="bg-white py-8 px-4 shadow-lg rounded-2xl sm:px-10">
        
        <div class="mb-8 border-b pb-4">
            <h2 class="text-3xl font-bold text-purple-700">Registrasi Penjual</h2>
            <p class="mt-2 text-sm text-gray-500">Mulai jualan dengan mendaftarkan toko Anda.</p>
        </div>

        <form class="space-y-6" @submit.prevent="handleRegister">
            
            <!-- INFORMASI AKUN -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3">Informasi Akun</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input v-model="form.email" @blur="validateField('email')" type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm" :class="{'border-red-500': errors.email}">
                        <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                        <input v-model="form.password" @input="validateField('password')" @blur="validateField('password')" type="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm" :class="{'border-red-500': errors.password}">
                        <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
                    </div>
                </div>
            </div>

            <!-- DATA PRIBADI -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input v-model="form.namaPenjual" @blur="validateField('namaPenjual')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p v-if="errors.namaPenjual" class="mt-1 text-xs text-red-600">{{ errors.namaPenjual }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input v-model="form.nik" @input="validateField('nik')" @blur="validateField('nik')" type="text" maxlength="16" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" :class="{'border-red-500': errors.nik}">
                            <p v-if="errors.nik" class="mt-1 text-xs text-red-600">{{ errors.nik }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP (08xx) <span class="text-red-500">*</span></label>
                            <input v-model="form.noHp" @input="validateField('noHp')" @blur="validateField('noHp')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" :class="{'border-red-500': errors.noHp}">
                            <p v-if="errors.noHp" class="mt-1 text-xs text-red-600">{{ errors.noHp }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Profil <span class="text-red-500">*</span></label>
                            <input @change="handleFileChange($event, 'foto')" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            <p v-if="errors.foto" class="mt-1 text-xs text-red-600">{{ errors.foto }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto KTP <span class="text-red-500">*</span></label>
                            <input @change="handleFileChange($event, 'ktp')" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            <p v-if="errors.fotoKtp" class="mt-1 text-xs text-red-600">{{ errors.fotoKtp }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATA TOKO -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Data Toko</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Toko <span class="text-red-500">*</span></label>
                        <input v-model="form.namaToko" @blur="validateField('namaToko')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p v-if="errors.namaToko" class="mt-1 text-xs text-red-600">{{ errors.namaToko }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Toko <span class="text-red-500">*</span></label>
                        <textarea v-model="form.deskripsiToko" @input="validateField('deskripsiToko')" @blur="validateField('deskripsiToko')" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm" :class="{'border-red-500': errors.deskripsiToko}"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-xs text-red-600 h-4">{{ errors.deskripsiToko }}</p>
                            <p class="text-xs text-gray-400">{{ form.deskripsiToko.length }}/20</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALAMAT LENGKAP (CASCADING DROPDOWN) -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Alamat Lengkap</h3>
                <div class="space-y-4">
                    
                    <!-- PROVINSI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                        <select 
                            v-model="selectedRegionIds.provinsi" 
                            @change="handleProvinsiChange" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white"
                            :class="{'border-red-500': errors.provinsi}"
                        >
                            <option value="" disabled>-- Pilih Provinsi --</option>
                            <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
                        </select>
                        <p v-if="errors.provinsi" class="mt-1 text-xs text-red-600">{{ errors.provinsi }}</p>
                    </div>

                    <!-- KOTA/KABUPATEN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
                        <select 
                            v-model="selectedRegionIds.kota" 
                            @change="handleKotaChange"
                            :disabled="!selectedRegionIds.provinsi"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100 disabled:text-gray-400"
                            :class="{'border-red-500': errors.kota}"
                        >
                            <option value="" disabled>-- Pilih Kota/Kab --</option>
                            <option v-for="city in regencies" :key="city.id" :value="city.id">{{ city.name }}</option>
                        </select>
                        <p v-if="errors.kota" class="mt-1 text-xs text-red-600">{{ errors.kota }}</p>
                    </div>

                    <!-- KECAMATAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select 
                            v-model="selectedRegionIds.kecamatan"
                            @change="handleKecamatanChange" 
                            :disabled="!selectedRegionIds.kota"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100 disabled:text-gray-400"
                            :class="{'border-red-500': errors.kecamatan}"
                        >
                            <option value="" disabled>-- Pilih Kecamatan --</option>
                            <option v-for="dist in districts" :key="dist.id" :value="dist.id">{{ dist.name }}</option>
                        </select>
                        <p v-if="errors.kecamatan" class="mt-1 text-xs text-red-600">{{ errors.kecamatan }}</p>
                    </div>

                    <!-- DESA/KELURAHAN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa <span class="text-red-500">*</span></label>
                        <select 
                            v-model="selectedRegionIds.desa"
                            @change="handleDesaChange"
                            :disabled="!selectedRegionIds.kecamatan"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100 disabled:text-gray-400"
                            :class="{'border-red-500': errors.desa}"
                        >
                            <option value="" disabled>-- Pilih Desa --</option>
                            <option v-for="vill in villages" :key="vill.id" :value="vill.id">{{ vill.name }}</option>
                        </select>
                        <p v-if="errors.desa" class="mt-1 text-xs text-red-600">{{ errors.desa }}</p>
                    </div>

                    <!-- JALAN & RT/RW -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jalan <span class="text-red-500">*</span></label>
                        <input v-model="form.jalan" @blur="validateField('jalan')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p v-if="errors.jalan" class="mt-1 text-xs text-red-600">{{ errors.jalan }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input v-model="form.rt" @blur="validateField('rt')" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input v-model="form.rw" @blur="validateField('rw')" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        </div>
                    </div>

                </div>
            </div>

            <div class="pt-4">
                <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="isLoading">Memproses...</span>
                    <span v-else>Daftar Sekarang</span>
                </button>
            </div>
        </form>

         <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Sudah punya akun? <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">Masuk di sini</router-link>
            </p>
          </div>
      </div>
    </div>
  </div>
</template>