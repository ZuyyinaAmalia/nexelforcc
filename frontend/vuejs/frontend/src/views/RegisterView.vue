<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { penjualRegister } from '@/services';

const router = useRouter();
const isLoading = ref(false);

// --- DATA PROVINSI ---
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

// --- STATE ---
const form = ref({
    email: '', password: '', namaPenjual: '', nik: '', noHp: '',
    namaToko: '', deskripsiToko: '', jalan: '', rt: '', rw: '',
    desa: '', kota: '', provinsi: ''
});

const errors = ref<Record<string, string>>({
    email: '', password: '', namaPenjual: '', nik: '', noHp: '',
    namaToko: '', deskripsiToko: '', jalan: '', rt: '', rw: '',
    desa: '', kota: '', provinsi: '', foto: '', fotoKtp: ''
});

const fileFoto = ref<File | null>(null);
const fileKtp = ref<File | null>(null);
const showProvinsiList = ref(false);

// --- LOGIC ---

// 1. Filter Provinsi
const filteredProvinsi = computed(() => {
    if (!form.value.provinsi) return provinsiList;
    return provinsiList.filter(p => 
        p.toLowerCase().includes(form.value.provinsi.toLowerCase())
    );
});

// Menggunakan @mousedown.prevent di template agar tidak bentrok dengan @blur
const selectProvinsi = (prov: string) => {
    form.value.provinsi = prov;
    showProvinsiList.value = false;
    validateField('provinsi');
};

const handleProvinsiBlur = () => {
    // Delay sedikit agar klik dropdown sempat tereksekusi
    setTimeout(() => {
        showProvinsiList.value = false;
        validateField('provinsi');
    }, 200);
};

// 2. Handle File Upload (Fix TypeScript)
const handleFileChange = (e: Event, type: 'foto' | 'ktp') => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        // Force cast ke File atau null agar TypeScript tidak rewel
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

// 3. Validasi Field
const validateField = (field: string) => {
    const val = form.value[field as keyof typeof form.value];
    errors.value[field] = ''; // Reset

    // Cek Kosong
    if (!val || val === '') {
        errors.value[field] = 'Wajib diisi';
        return;
    }

    // Validasi Spesifik
    if (field === 'password') {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!regex.test(val as string)) {
            errors.value.password = "Min 8 kar, Besar, kecil, angka & simbol.";
        }
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

// 4. Handle Register (Kirim ke Backend)
const handleRegister = async () => {
    let isValid = true;
    
    // Validasi Text
    Object.keys(form.value).forEach(key => {
        validateField(key);
        if (errors.value[key]) isValid = false;
    });

    // Validasi File
    if (!fileFoto.value) { errors.value.foto = "Wajib upload"; isValid = false; }
    if (!fileKtp.value) { errors.value.fotoKtp = "Wajib upload"; isValid = false; }

    if (!isValid) {
        alert("Mohon perbaiki isian yang berwarna merah.");
        return;
    }

    // --- PROSES KIRIM ---
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
        console.error(error);
        if (error.response && error.response.data && error.response.data.message) {
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

            <!-- ALAMAT LENGKAP -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Alamat Lengkap</h3>
                <div class="space-y-4">
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kelurahan/Desa <span class="text-red-500">*</span></label>
                            <input v-model="form.desa" @blur="validateField('desa')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p v-if="errors.desa" class="mt-1 text-xs text-red-600">{{ errors.desa }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
                            <input v-model="form.kota" @blur="validateField('kota')" type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p v-if="errors.kota" class="mt-1 text-xs text-red-600">{{ errors.kota }}</p>
                        </div>
                    </div>

                    <!-- PROVINSI SEARCHABLE -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                        <input 
                            v-model="form.provinsi" 
                            @focus="showProvinsiList = true"
                            @blur="handleProvinsiBlur" 
                            type="text" 
                            placeholder="Cari Provinsi..."
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            autocomplete="off"
                        >
                        <!-- Dropdown (PENTING: pakai mousedown.prevent) -->
                        <ul v-if="showProvinsiList && filteredProvinsi.length > 0" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            <li 
                                v-for="prov in filteredProvinsi" 
                                :key="prov"
                                @mousedown.prevent="selectProvinsi(prov)" 
                                class="text-gray-900 cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-purple-100"
                            >
                                <span class="font-normal block truncate">{{ prov }}</span>
                            </li>
                        </ul>
                        <div v-if="showProvinsiList && filteredProvinsi.length === 0" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-3 text-sm text-gray-500">
                            Provinsi tidak ditemukan.
                        </div>
                        <p v-if="errors.provinsi" class="mt-1 text-xs text-red-600">{{ errors.provinsi }}</p>
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