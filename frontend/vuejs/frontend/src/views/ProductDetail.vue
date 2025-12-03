<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from 'axios';
import { useRoute } from 'vue-router';

// --- INIT & CONFIG ---
const route = useRoute();
const productId = useRoute().params.id; 
const API_URL = `http://127.0.0.1:8000/api/public/produks/${productId}`; 
const LARAVEL_BASE_URL = 'http://127.0.0.1:8000';
const REVIEW_API_URL = `${LARAVEL_BASE_URL}/api/public/reviews`;

// --- STATE DATA ---
const product = ref<any>(null);
const isLoading = ref(true); 
const error = ref<string | null>(null); 

// Data dummy ulasan - GANTI INI menjadi array kosong agar data diambil dari API
const reviews = ref<any[]>([]); // GANTI: Gunakan array kosong untuk data ulasan dari API

// STATE untuk Tab
const activeTab = ref('description');

// --- STATE BARU UNTUK FORM ULASAN (MODAL) ---
const isReviewModalOpen = ref(false); 
const reviewForm = ref({
    user: '',
    phone: '',
    email: '',
    comment: '',
    rating: 5, // Default rating 5
    province: '',
});

// ⭐ STATE BARU UNTUK MODAL SUKSES
const isSuccessModalOpen = ref(false);

// --- LIST PROVINSI BARU ---
const PROVINSI_LIST = [
    'Nanggroe Aceh Darussalam', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau',
    'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Bangka Belitung',
    'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
    'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
    'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
    'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
    'Maluku', 'Maluku Utara',
    'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
];
// --- END LIST PROVINSI ---


// --- FUNGSI UTAMA ---

// FUNGSI BARU: Menghitung rata-rata rating dari array ulasan
const calculateAverageRating = (reviewsList: any[]): number => {
    // Pastikan list ulasan tidak null atau kosong
    if (!reviewsList || reviewsList.length === 0) {
        return 0;
    }
    
    // Hitung total semua nilai rating
    const totalRating = reviewsList.reduce((sum, review) => {
        // Menggunakan parseInt untuk memastikan nilai rating adalah angka
        return sum + (parseInt(review.rating) || 0); 
    }, 0);
    
    // Hitung rata-rata dan bulatkan ke satu desimal
    const average = totalRating / reviewsList.length;
    // Menggunakan parseFloat(average.toFixed(1)) untuk membulatkan ke 1 angka di belakang koma
    return parseFloat(average.toFixed(1)); 
};

// --- FUNGSI UTAMA YANG DIMODIFIKASI ---
const fetchProductDetail = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        const response = await axios.get(API_URL);
        const apiData = response.data; 

        // ⭐ PEMBENAHAN: Definisikan array ulasan yang sudah dipetakan terlebih dahulu.
        // Ini memastikan reviews.value terisi sebelum product.value diatur.
        let mappedReviews: any[] = [];
        if (apiData.reviews && apiData.reviews.length > 0) {
            mappedReviews = apiData.reviews.map((review: any) => ({
                id: review.id,
                user: review.namaPengunjung, 
                rating: review.rating,
                date: new Date(review.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }),
                comment: review.ulasan, 
                province: review.provinsiPengunjung, // <-- BARU
                email: review.emailPengunjung,       // <-- BARU
            })).reverse(); // Terbaru di atas
        }
        
        // ⭐ PEMBENAHAN: Setel reviews.value DI SINI
        reviews.value = mappedReviews; // Ini akan mengupdate tab "Ulasan Pembeli (X)"

        // Mapping Data Produk ke state `product`
        product.value = {
            id: apiData.id,
            name: apiData.namaProduk,
            price: apiData.harga,
            description: apiData.deskripsi,
            condition: apiData.kondisi,
            stok: apiData.stok,
            
            seller: {
                name: apiData.penjual ? apiData.penjual.namaToko : 'Penjual Tidak Dikenal',
                city: apiData.penjual ? apiData.penjual.namaPenjual : 'N/A', 
                province: 'Online Store',
            },
            
            category: [apiData.kategori ? apiData.kategori.namaKategori : 'Tidak Berkategori', 'Detail'],
            
            image: apiData.fotoProduk
                ? `${LARAVEL_BASE_URL}/storage/${apiData.fotoProduk}`
                : 'https://images.unsplash.com/photo-1695048134431-e92db4cf1975?q=80&w=1000',
            
            // ⭐ PERBAIKAN: Menggunakan fungsi calculateAverageRating dengan `mappedReviews`
            rating: calculateAverageRating(mappedReviews), 

            // ⭐ PERBAIKAN: Menggunakan panjang dari `mappedReviews`
            totalReviews: mappedReviews.length,
        };
        
    } catch (err: any) {
        console.error("Gagal mengambil detail produk:", err);
        if (err.response && err.response.status === 404) {
            error.value = `Produk dengan ID ${productId} tidak ditemukan.`;
        } else {
            error.value = "Gagal memuat detail produk. Cek koneksi server Laravel.";
        }
    } finally {
        isLoading.value = false;
    }
};




// --- FUNGSI LOGIC MODAL ---
// FUNGSI BARU UNTUK MODAL SUKSES
const openSuccessModal = () => {
    isSuccessModalOpen.value = true;
};

const closeSuccessModal = () => {
    isSuccessModalOpen.value = false;
};

const openReviewModal = () => {
    isReviewModalOpen.value = true;
};

const closeReviewModal = () => {
    isReviewModalOpen.value = false;
    // Reset formulir setelah ditutup
    reviewForm.value = {
        user: '',
        phone: '',
        email: '',
        comment: '',
        rating: 5,
        province: '',
    };
};


const submitReview = async () => { 
    // 1. Validasi Sisi Frontend (Pencegahan Dasar)
    if (!reviewForm.value.rating || !reviewForm.value.user || !reviewForm.value.comment || !reviewForm.value.province || !reviewForm.value.phone || !reviewForm.value.email) {
        alert("Semua field (Rating, Nama, Komentar, Provinsi, No. HP, dan Email) wajib diisi!");
        return;
    }

    try {
        const payload = {
            // Pastikan keys ini cocok 100% dengan validation rules di ReviewController
            produk_id: productId, 
            rating: reviewForm.value.rating,
            ulasan: reviewForm.value.comment,
            namaPengunjung: reviewForm.value.user,
            emailPengunjung: reviewForm.value.email,
            noHpPengunjung: reviewForm.value.phone,
            provinsiPengunjung: reviewForm.value.province,
        };
        
        const response = await axios.post(REVIEW_API_URL, payload);
        
        console.log("Ulasan berhasil dikirim:", response.data);

        // Setelah sukses, muat ulang data produk dan ulasan
        await fetchProductDetail(); 
        
        // ⭐ PERUBAHAN: Tutup modal review dan Buka modal sukses
        closeReviewModal(); 
        openSuccessModal(); 
        
    } catch (err: any) {
        // ... (Logic error handling Anda sebelumnya)
        console.error("Gagal mengirim ulasan:", err);
        
        let errorMessage = "Gagal mengirim ulasan. Pastikan server Laravel berjalan dan koneksi internet stabil.";
        
        if (err.response) {
            if (err.response.status === 422) {
                const errors = err.response.data.errors;
                let validationMessage = "Validasi Gagal (422):\n";
                for (const key in errors) {
                    validationMessage += `- ${errors[key][0]}\n`;
                }
                errorMessage = validationMessage;

            } else if (err.response.data.message) {
                errorMessage = `Error Server (${err.response.status}): ${err.response.data.message}`;
            } else {
                 errorMessage = `Error HTTP (${err.response.status}). Kemungkinan masalah CORS atau Endpoint salah.`;
            }
        }
        
        alert(errorMessage); 
    }
};


// Panggil fungsi saat komponen selesai dimuat
onMounted(fetchProductDetail);

// Fungsi helper untuk menampilkan bintang
const getStarDisplay = (rating: number) => {
    const fullStar = '★';
    const emptyStar = '☆';
    return fullStar.repeat(rating) + emptyStar.repeat(5 - rating);
};

// Fungsi helper untuk format harga
const formatPrice = (price: number) => {
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return '0';
    return numericPrice.toLocaleString('id-ID');
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 font-sans">

        <header class="bg-white shadow-md sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center gap-6">
                
                <h1 class="text-3xl font-extrabold text-purple-600 cursor-pointer pr-4">NEXEL</h1>
                
                <div class="flex-grow relative">
                    <input
                        type="text"
                        placeholder="Cari produk di Nexel..."
                        class="w-full py-3 pl-4 pr-12 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    />
                    <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex items-center gap-3 ml-4">
                    <button class="px-4 py-2 border border-purple-600 text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition">
                        Masuk
                    </button>
                    <button class="px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">
                        Daftar
                    </button>
                </div>
            </div>
        </header>
        
        <div class="max-w-7xl mx-auto px-6 py-4">

            <div v-if="isLoading" class="text-center py-20 text-gray-500">
                <p>Memuat detail produk...</p>
                <svg class="animate-spin h-6 w-6 mx-auto mt-4 text-purple-600" viewBox="0 0 24 24"></svg>
            </div>

            <div v-else-if="error" class="text-center py-20 text-red-500 border border-red-200 p-8 rounded-xl">
                <h2 class="text-xl font-bold mb-2">{{ error }}</h2>
                <p class="text-sm">Pastikan ID produk benar dan server Laravel berjalan.</p>
            </div>


            <div v-else-if="product">
                
                <div class="text-sm text-gray-600 mb-4">
                    <router-link to="/home" class="cursor-pointer hover:text-purple-600">Beranda</router-link> ›
                    <span class="cursor-pointer hover:text-purple-600">{{ product.category[0] }}</span> ›
                    <span class="font-medium text-gray-800">{{ product.name }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                    <div class="bg-white p-6 rounded-2xl shadow">
                        <div class="w-full aspect-square overflow-hidden">
                            <img :src="product.image" :alt="product.name" class="rounded-xl w-full h-full object-cover" />
                        </div>
                    </div>

                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ product.name }}</h2>

                        <span 
                            :class="[
                                'inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3',
                                product.condition === 'Baru' 
                                    ? 'bg-green-100 text-green-800' // Styling untuk Baru
                                    : 'bg-yellow-100 text-yellow-800' // Styling untuk Bekas
                            ]"
                        >
                            {{ product.condition }}
                        </span>


                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-yellow-400 text-xl">★</span>
                            <span class="font-medium text-gray-800">{{ product.rating }}</span> 
                            <span class="text-gray-500">({{ product.totalReviews }} ulasan)</span>
                        </div>

                        <div class="mb-4 space-y-2 text-gray-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10h16V7M4 7h16M4 7l-2-2m20 0l-2-2m-10 16v-4m0 0H6m6 0h12"></path></svg>
                                <span class="font-medium">Kategori:</span> 
                                <span>{{ product.category[0] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <span class="font-medium">Stok:</span> 
                                <span :class="{'text-red-500 font-semibold': product.stok <= 5}">{{ product.stok > 0 ? product.stok + ' Unit' : 'Habis' }}</span>
                            </div>
                        </div>
                        <p class="text-4xl font-extrabold text-red-600 my-6"> 
                            Rp {{ formatPrice(product.price) }}
                        </p>


                        <div class="flex gap-4"> 
                            <button 
                                @click="openReviewModal"
                                class="bg-purple-600 hover:bg-purple-700 text-white w-full py-3 rounded-xl font-semibold transition text-lg shadow-lg shadow-purple-200/50"
                            >
                                Tulis Ulasan
                            </button>
                        </div>
                        <div class="mt-8 p-5 bg-white rounded-xl shadow">
                            <h3 class="font-semibold text-gray-800 text-lg mb-2">Informasi Penjual</h3>
                            <p class="text-gray-700 font-medium">{{ product.seller.name }}</p>
                            <p class="text-gray-500">{{ product.seller.city }}, {{ product.seller.province }}</p>
                        </div>
                    </div>

                    <div class="md:col-span-2 mt-8 mb-20"> 
                        <div class="bg-white p-6 rounded-2xl shadow">
                            
                            <div class="flex border-b border-gray-200 mb-3"> 
                                <button 
                                    @click="activeTab = 'description'"
                                    :class="[
                                        'px-4 py-2 text-lg font-semibold border-b-2 transition-colors duration-200',
                                        activeTab === 'description' 
                                            ? 'text-purple-600 border-purple-600' 
                                            : 'text-gray-500 border-transparent hover:text-purple-500'
                                    ]"
                                >
                                    Deskripsi Produk
                                </button>

                                <button 
                                    @click="activeTab = 'reviews'"
                                    :class="[
                                        'px-4 py-2 text-lg font-semibold border-b-2 transition-colors duration-200',
                                        activeTab === 'reviews' 
                                            ? 'text-purple-600 border-purple-600' 
                                            : 'text-gray-500 border-transparent hover:text-purple-500'
                                    ]"
                                >
                                    Ulasan Pembeli ({{ reviews.length }})
                                </button>
                            </div>

                            <div class="tab-content">
                                
                                <div v-if="activeTab === 'description'">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                        {{ product.description }}
                                    </p>
                                </div>




                            <div v-if="activeTab === 'reviews'">
    <div class="space-y-6">
        <div v-for="review in reviews" :key="review.id" class="border-b pb-4 last:border-b-0 last:pb-0">

            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-800">{{ review.user }}</span>
                    <span class="text-xs text-gray-500 hidden sm:inline">-</span> 
                    <span class="text-sm text-gray-500 italic">{{ review.email }}</span>
                </div>
                
                <span class="text-sm text-gray-500">{{ review.date }}</span>
            </div>

            <p class="text-xs text-purple-600 font-medium mb-1">
                <svg class="w-3 h-3 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ review.province }}
            </p>
            
            <div class="flex items-center mb-2">
                <span class="text-yellow-500 text-lg mr-2">{{ getStarDisplay(review.rating) }}</span>
                <span class="text-sm font-medium text-gray-700">{{ review.rating }}/5</span>
            </div>

            <p class="text-gray-700 italic">"{{ review.comment }}"</p>
        </div>
    </div>
</div>




                                
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="bg-[#1E2532] text-white py-12 mt-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 border-b border-gray-700 pb-8 mb-8">
                    
                    <div>
                        <h3 class="text-3xl font-extrabold text-purple-400 mb-3">NEXEL</h3>
                        <p class="text-sm text-gray-400">
                            Pusat belanja kebutuhan mahasiswa terbaik di Indonesia.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Layanan & Bantuan</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Cara Belanja</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Pengembalian Dana</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Hubungi Kami</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">FAQ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Perusahaan</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Tentang Kami</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Karir</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Kebijakan Privasi</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Pembayaran & Logistik</h4>
                        <div class="flex flex-wrap gap-2 text-2xl text-gray-400">
                            <span title="Visa">💳</span>
                            <span title="Mastercard">💲</span>
                            <span title="Transfer Bank">🏦</span>
                            <span title="JNE">🚚</span>
                            <span title="SiCepat">📦</span>
                            <span title="Gojek">🛵</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p class="mb-4 md:mb-0">
                        &copy; 2025 NEXEL. Hak Cipta Dilindungi.
                    </p>
                </div>
            </div>
        </footer>
        
        <div v-if="isReviewModalOpen" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeReviewModal"></div>

            <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-lg mx-4 relative transform transition-all duration-300 scale-100">
                
                <h3 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-2">Tulis Ulasan Produk</h3>
                
                <form @submit.prevent="submitReview" class="space-y-4">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating ({{ reviewForm.rating }} Bintang)</label>
                        <div class="flex items-center space-x-1">
                            <span 
                                v-for="n in 5" :key="n"
                                @click="reviewForm.rating = n"
                                :class="[
                                    'text-3xl cursor-pointer transition-colors',
                                    n <= reviewForm.rating ? 'text-yellow-500' : 'text-gray-300 hover:text-yellow-400'
                                ]"
                            >
                                ★
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="user" class="block text-sm font-medium text-gray-700">Nama Anda</label>
                        <input 
                            type="text" id="user" v-model="reviewForm.user" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">No. HP (Wajib)</label>
                            <input 
                                type="tel" id="phone" v-model="reviewForm.phone" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" 
                                required 
                            />
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (Wajib)</label>
                            <input 
                                type="email" id="email" v-model="reviewForm.email" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" 
                                required 
                            />
                        </div>
                    </div>
                    
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700">Provinsi Asal</label>
                        <select 
                            id="province" v-model="reviewForm.province" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500 bg-white"
                        >
                            <option value="" disabled>-- Pilih Provinsi --</option>
                            <option v-for="provinsi in PROVINSI_LIST" :key="provinsi" :value="provinsi">
                                {{ provinsi }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700">Komentar</label>
                        <textarea 
                            id="comment" v-model="reviewForm.comment" rows="3" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-purple-500 focus:border-purple-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button 
                            type="button" @click="closeReviewModal"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md font-semibold hover:bg-purple-700 transition"
                        >
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="isSuccessModalOpen" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeSuccessModal"></div>

            <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-sm mx-4 relative transform transition-all duration-300 scale-100 text-center">
                
                <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                
                <h3 class="text-xl font-bold text-gray-900 mb-3">Ulasan Terkirim!</h3>
                <p class="text-gray-600 mb-6">Terima kasih atas ulasan Anda. Kami akan segera menampilkannya.</p>
                
                <button 
                    @click="closeSuccessModal"
                    class="w-full px-4 py-3 bg-purple-600 text-white rounded-md font-semibold hover:bg-purple-700 transition"
                >
                    Tutup
                </button>
            </div>
        </div>
        
    </div> 
</template>