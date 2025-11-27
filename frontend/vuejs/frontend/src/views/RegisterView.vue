<script setup lang="ts">
import { ref, computed } from 'vue';

// --- DATA ---
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

// State Form Data
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

// State Error Messages (Untuk menampung pesan merah)
const errors = ref<Record<string, string>>({
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
    provinsi: '',
    foto: '',
    fotoKtp: ''
});

// State File
const fileFoto = ref<File | null>(null);
const fileKtp = ref<File | null>(null);

// State untuk Dropdown Provinsi
const showProvinsiList = ref(false);

// --- LOGIC ---

// 1. Filter Provinsi (Searchable Dropdown)
const filteredProvinsi = computed(() => {
    if (!form.value.provinsi) return provinsiList;
    return provinsiList.filter(p => 
        p.toLowerCase().includes(form.value.provinsi.toLowerCase())
    );
});

const selectProvinsi = (prov: string) => {
    form.value.provinsi = prov;
    showProvinsiList.value = false;
    validateField('provinsi'); // Hapus error jika sudah pilih
};

// 2. Handle File Upload
// Ganti fungsi handleFileChange yang lama dengan ini:
const handleFileChange = (e: Event, type: 'foto' | 'ktp') => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        // Ambil file, jika undefined jadikan null
        const file = target.files[0] ?? null; 

        if (type === 'foto') {
            fileFoto.value = file;
            errors.value.foto = '';
        } else {
            fileKtp.value = file;
            errors.value.fotoKtp = '';
        }
    }
};

// 3. FUNGSI VALIDASI UTAMA (Dipanggil saat mengetik / pindah kolom)
const validateField = (field: string) => {
    const val = form.value[field as keyof typeof form.value];
    
    // Reset error dulu
    errors.value[field] = '';

    // Cek Wajib Diisi (General)
    if (!val || val === '') {
        errors.value[field] = 'Wajib diisi';
        return;
    }

    // Validasi Khusus per Field
    if (field === 'password') {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!regex.test(val as string)) {
            errors.value.password = "Min. 8 karakter, huruf besar, kecil, angka & simbol.";
        }
    }

    if (field === 'nik') {
        // Cek harus angka dan 16 digit
        if (!/^\d+$/.test(val as string)) {
            errors.value.nik = "NIK harus berupa angka.";
        } else if ((val as string).length !== 16) {
            errors.value.nik = `NIK harus 16 digit (Saat ini: ${(val as string).length})`;
        }
    }

    if (field === 'noHp') {
        // Cek depannya harus 08
        if (!(val as string).startsWith('08')) {
            errors.value.noHp = "Nomor HP harus diawali '08'.";
        } else if ((val as string).length < 10 || (val as string).length > 14) {
            errors.value.noHp = "Panjang nomor HP tidak valid.";
        }
    }

    if (field === 'deskripsiToko') {
        if ((val as string).length < 100) {
            errors.value.deskripsiToko = `Deskripsi terlalu pendek (Min. 100 karakter, Saat ini: ${(val as string).length})`;
        }
    }
};

// Tambahkan fungsi baru ini untuk menangani blur pada provinsi
const handleProvinsiBlur = () => {
    setTimeout(() => {
        showProvinsiList.value = false;
        validateField('provinsi');
    }, 200);
};

// 4. Handle Submit
const handleRegister = () => {
    // Cek semua validasi sebelum kirim
    let isValid = true;
    
    // Loop semua key di form untuk divalidasi manual
    Object.keys(form.value).forEach(key => {
        validateField(key);
        if (errors.value[key]) isValid = false;
    });

    // Cek file manual
    if (!fileFoto.value) { errors.value.foto = "Foto Profil wajib diupload"; isValid = false; }
    if (!fileKtp.value) { errors.value.fotoKtp = "Foto KTP wajib diupload"; isValid = false; }

    if (!isValid) {
        alert("Mohon perbaiki isian yang berwarna merah.");
        return;
    }

    // Jika lolos semua, kirim data
    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
        formData.append(key, form.value[key as keyof typeof form.value]);
    });
    if (fileFoto.value) formData.append('foto', fileFoto.value);
    if (fileKtp.value) formData.append('fotoKtp', fileKtp.value);

    console.log("Data Valid! Mengirim ke Backend...");
    // Panggil API Backend di sini
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
      <div class="bg-white py-8 px-4 shadow-lg rounded-2xl sm:px-10">
        
        <div class="mb-8 border-b pb-4">
            <h2 class="text-3xl font-bold text-purple-700 mb-3">Registrasi Penjual</h2>
            <p class="mt-2 text-sm text-gray-500">Mulai jualan dengan mendaftarkan toko Anda.</p>
        </div>

        <form class="space-y-6" @submit.prevent="handleRegister">
            
            <!-- INFORMASI AKUN -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3">Informasi Akun</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input 
                            v-model="form.email" 
                            @blur="validateField('email')"
                            type="email" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': errors.email}"
                        >
                        <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email }}</p>
                    </div>

                    <!-- Password (Live Validation) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                        <input 
                            v-model="form.password" 
                            @input="validateField('password')"
                            @blur="validateField('password')"
                            type="password" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            :class="{'border-red-500': errors.password}"
                        >
                        <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
                    </div>
                </div>
            </div>

            <!-- DATA PRIBADI -->
            <div>
                <h3 class="text-lg font-medium text-purple-700 mb-3 mt-4 border-t pt-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input 
                            v-model="form.namaPenjual" 
                            @blur="validateField('namaPenjual')"
                            type="text" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                        >
                        <p v-if="errors.namaPenjual" class="mt-1 text-xs text-red-600">{{ errors.namaPenjual }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK (16 Digit) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input 
                                v-model="form.nik" 
                                @input="validateField('nik')"
                                @blur="validateField('nik')"
                                type="text" 
                                maxlength="16" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                :class="{'border-red-500': errors.nik}"
                            >
                            <p v-if="errors.nik" class="mt-1 text-xs text-red-600">{{ errors.nik }}</p>
                        </div>

                        <!-- No HP (Format 08) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP (Format: 08xx) <span class="text-red-500">*</span></label>
                            <input 
                                v-model="form.noHp" 
                                @input="validateField('noHp')"
                                @blur="validateField('noHp')"
                                type="text" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                :class="{'border-red-500': errors.noHp}"
                            >
                            <p v-if="errors.noHp" class="mt-1 text-xs text-red-600">{{ errors.noHp }}</p>
                        </div>
                    </div>

                    <!-- Uploads -->
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
                    
                    <!-- Deskripsi Toko (Min 100 Karakter) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Deskripsi Toko <span class="text-red-500">*</span><span class="text-xs text-gray-500">(Min. 100 karakter)</span> 
                        </label>
                        <textarea 
                            v-model="form.deskripsiToko" 
                            @input="validateField('deskripsiToko')"
                            @blur="validateField('deskripsiToko')"
                            rows="3" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            :class="{'border-red-500': errors.deskripsiToko}"
                        ></textarea>
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
                        <input v-model="form.jalan" @blur="validateField('jalan')" type="text" placeholder="Jl. Mawar No. 12" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <p v-if="errors.jalan" class="mt-1 text-xs text-red-600">{{ errors.jalan }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RT <span class="text-red-500">*</span></label>
                            <input v-model="form.rt" @blur="validateField('rt')" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p v-if="errors.rt" class="mt-1 text-xs text-red-600">{{ errors.rt }}</p>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">RW <span class="text-red-500">*</span></label>
                            <input v-model="form.rw" @blur="validateField('rw')" type="text" maxlength="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p v-if="errors.rw" class="mt-1 text-xs text-red-600">{{ errors.rw }}</p>
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

                    <!-- PROVINSI DENGAN SEARCH/FILTER -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
                        
                        <!-- Input Pencarian -->
                        <input 
                            v-model="form.provinsi" 
                            @focus="showProvinsiList = true"
                            @blur="handleProvinsiBlur"
                            type="text" 
                            placeholder="Contoh: Jawa Tengah"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            autocomplete="off"
                        >
                        
                        <!-- Dropdown List Hasil Filter -->
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
                        <!-- Pesan jika tidak ketemu -->
                        <div v-if="showProvinsiList && filteredProvinsi.length === 0" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-3 text-sm text-gray-500">
                            Provinsi tidak ditemukan.
                        </div>

                        <p v-if="errors.provinsi" class="mt-1 text-xs text-red-600">{{ errors.provinsi }}</p>
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