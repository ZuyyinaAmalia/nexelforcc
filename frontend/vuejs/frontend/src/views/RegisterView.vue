<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
// import { penjualRegister } from '@/services/api'; // Uncomment jika sudah siap pakai

// Mocking API call untuk contoh agar tidak error saat dicoba (Hapus jika sudah pakai import asli)
const penjualRegister = async (data: any) => {
    return new Promise((resolve) => setTimeout(resolve, 1000));
};

const router = useRouter();
const isLoading = ref(false);

// --- API WILAYAH URL ---
const API_BASE_URL = 'https://www.emsifa.com/api-wilayah-indonesia/api';

// --- INTERFACES ---
interface Region {
    id: string;
    name: string;
}

// --- STATE WILAYAH (Lists) ---
const provinces = ref<Region[]>([]);
const regencies = ref<Region[]>([]); // Kota/Kab
const districts = ref<Region[]>([]); // Kecamatan
const villages = ref<Region[]>([]);  // Desa/Kelurahan

// --- STATE SELECTED IDs (Untuk Logic Fetching) ---
// Kita pisah antara ID (untuk fetch API) dan Nama (untuk dikirim ke Backend)
const selectedRegionIds = ref({
    provinsi: '',
    kota: '',
    kecamatan: '',
    desa: ''
});

// --- MAIN FORM STATE ---
const form = ref({
    email: '', password: '', namaPenjual: '', nik: '', noHp: '',
    namaToko: '', deskripsiToko: '', jalan: '', rt: '', rw: '',
    provinsi: '', kota: '', kecamatan: '', desa: '' // Disimpan sebagai Nama (String)
});

const errors = ref<Record<string, string>>({
    email: '', password: '', namaPenjual: '', nik: '', noHp: '',
    namaToko: '', deskripsiToko: '', jalan: '', rt: '', rw: '',
    provinsi: '', kota: '', kecamatan: '', desa: '', foto: '', fotoKtp: ''
});

const fileFoto = ref<File | null>(null);
const fileKtp = ref<File | null>(null);

// --- FETCHING LOGIC ---

// 1. Fetch Provinsi (Jalan saat mounted)
const fetchProvinces = async () => {
    try {
        const res = await fetch(`${API_BASE_URL}/provinces.json`);
        provinces.value = await res.json();
    } catch (e) {
        console.error("Gagal ambil provinsi", e);
    }
};

// 2. Fetch Kota/Kabupaten
const handleProvinsiChange = async () => {
    // Reset child fields
    regencies.value = []; districts.value = []; villages.value = [];
    selectedRegionIds.value.kota = ''; selectedRegionIds.value.kecamatan = ''; selectedRegionIds.value.desa = '';
    form.value.kota = ''; form.value.kecamatan = ''; form.value.desa = '';
    
    // Set Nama Provinsi ke Form
    const selectedProv = provinces.value.find(p => p.id === selectedRegionIds.value.provinsi);
    form.value.provinsi = selectedProv ? selectedProv.name : '';
    validateField('provinsi');

    // Fetch Kota
    if (selectedRegionIds.value.provinsi) {
        try {
            const res = await fetch(`${API_BASE_URL}/regencies/${selectedRegionIds.value.provinsi}.json`);
            regencies.value = await res.json();
        } catch (e) { console.error(e); }
    }
};

// 3. Fetch Kecamatan
const handleKotaChange = async () => {
    // Reset child fields
    districts.value = []; villages.value = [];
    selectedRegionIds.value.kecamatan = ''; selectedRegionIds.value.desa = '';
    form.value.kecamatan = ''; form.value.desa = '';

    // Set Nama Kota ke Form
    const selectedKota = regencies.value.find(r => r.id === selectedRegionIds.value.kota);
    form.value.kota = selectedKota ? selectedKota.name : '';
    validateField('kota');

    // Fetch Kecamatan
    if (selectedRegionIds.value.kota) {
        try {
            const res = await fetch(`${API_BASE_URL}/districts/${selectedRegionIds.value.kota}.json`);
            districts.value = await res.json();
        } catch (e) { console.error(e); }
    }
};

// 4. Fetch Desa/Kelurahan
const handleKecamatanChange = async () => {
    // Reset child fields
    villages.value = [];
    selectedRegionIds.value.desa = '';
    form.value.desa = '';

    // Set Nama Kecamatan ke Form
    const selectedKec = districts.value.find(d => d.id === selectedRegionIds.value.kecamatan);
    form.value.kecamatan = selectedKec ? selectedKec.name : '';
    validateField('kecamatan');

    // Fetch Desa
    if (selectedRegionIds.value.kecamatan) {
        try {
            const res = await fetch(`${API_BASE_URL}/villages/${selectedRegionIds.value.kecamatan}.json`);
            villages.value = await res.json();
        } catch (e) { console.error(e); }
    }
};

// 5. Set Desa Terakhir
const handleDesaChange = () => {
    const selectedDesa = villages.value.find(v => v.id === selectedRegionIds.value.desa);
    form.value.desa = selectedDesa ? selectedDesa.name : '';
    validateField('desa');
};

// --- INITIALIZE ---
onMounted(() => {
    fetchProvinces();
});

// --- HELPER & VALIDATION ---

const handleFileChange = (e: Event, type: 'foto' | 'ktp') => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0] || null; 
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

    if (!val || val === '') {
        errors.value[field] = 'Wajib diisi';
        return;
    }

    if (field === 'password') {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!regex.test(val as string)) errors.value.password = "Min 8 kar, Besar, kecil, angka & simbol.";
    }
    if (field === 'nik') {
        if (!/^\d+$/.test(val as string)) errors.value.nik = "Harus angka.";
        else if ((val as string).length !== 16) errors.value.nik = "Harus 16 digit.";
    }
    if (field === 'noHp') {
        if (!(val as string).startsWith('08')) errors.value.noHp = "Harus diawali '08'.";
        else if ((val as string).length < 10 || (val as string).length > 14) errors.value.noHp = "Panjang tidak valid.";
    }
    if (field === 'deskripsiToko') {
        if ((val as string).length < 100) errors.value.deskripsiToko = `Kurang panjang (${(val as string).length}/100)`;
    }
};

const handleRegister = async () => {
    let isValid = true;
    
    Object.keys(form.value).forEach(key => {
        validateField(key);
        if (errors.value[key]) isValid = false;
    });

    if (!fileFoto.value) { errors.value.foto = "Wajib upload"; isValid = false; }
    if (!fileKtp.value) { errors.value.fotoKtp = "Wajib upload"; isValid = false; }

    if (!isValid) {
        alert("Mohon perbaiki isian yang berwarna merah.");
        return;
    }

    isLoading.value = true;
    const formData = new FormData();
    
    Object.keys(form.value).forEach(key => {
        formData.append(key, form.value[key as keyof typeof form.value]);
    });
    if (fileFoto.value) formData.append('foto', fileFoto.value);
    if (fileKtp.value) formData.append('fotoKtp', fileKtp.value);

    try {
        await penjualRegister(formData);
        alert("Registrasi Berhasil! Silakan Login.");
        router.push('/login');
    } catch (error: any) {
        console.error(error);
        if (error.response?.data?.message) {
            alert("Gagal: " + error.response.data.message);
        } else {
            alert("Terjadi kesalahan pada server.");
        }
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
                            <p class="text-xs text-gray-400">{{ form.deskripsiToko.length }}/100</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ALAMAT LENGKAP (WILAYAH API) -->
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
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100"
                        >
                            <option value="" disabled>-- Pilih Kota/Kab --</option>
                            <option v-for="city in regencies" :key="city.id" :value="city.id">{{ city.name }}</option>
                        </select>
                        <p v-if="errors.kota" class="mt-1 text-xs text-red-600">{{ errors.kota }}</p>
                    </div>

                    <!-- KECAMATAN (BARU) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <select 
                            v-model="selectedRegionIds.kecamatan"
                            @change="handleKecamatanChange" 
                            :disabled="!selectedRegionIds.kota"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100"
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
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm bg-white disabled:bg-gray-100"
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