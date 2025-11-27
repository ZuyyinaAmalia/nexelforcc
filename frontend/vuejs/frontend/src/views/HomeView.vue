<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from 'axios';
import heroImage from '@/assets/hero.png';
// URL API untuk daftar semua produk
const API_URL = 'http://127.0.0.1:8000/api/produks'; 

const LARAVEL_BASE_URL = 'http://127.0.0.1:8000'; // Tambahkan ini
// --- STATE DATA ---

// State untuk menyimpan daftar produk dari API
const products = ref<any[]>([]); 
// State untuk menunjukkan proses loading
const isLoading = ref(true); 
// State untuk menyimpan pesan error
const error = ref<string | null>(null); 

// Dummy data untuk kategori (belum ada API Kategori)
const categories = ref([
    { name: 'Fashion Pria', icon: '👕' },
    { name: 'Elektronik', icon: '📱' },
    { name: 'Makanan & Minuman', icon: '🍔' },
    { name: 'Kecantikan', icon: '💅' },
    { name: 'Rumah Tangga', icon: '🛋️' },
    { name: 'Olahraga', icon: '⚽' },
    { name: 'Otomotif', icon: '🚗' },
    { name: 'Hobi & Koleksi', icon: '🎨' },
]);


// Fungsi untuk mengambil daftar produk dari Laravel
const fetchProducts = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        const response = await axios.get(API_URL);
        
        // Asumsi: Controller index() Anda menggunakan ProdukResource::collection
        const apiProducts = response.data.data;

        // Lakukan mapping dan simpan ke state products
        products.value = apiProducts.map((p: any) => ({
            id: p.id,
            name: p.nama_produk, 
            price: p.harga, 
            rating: 4.8, // Dummy rating
            totalReviews: 126, // Dummy review count
            // Menggunakan nama_toko dari relasi Penjual di Resource
            sellerCity: p.penjual ? p.penjual.nama_toko : 'N/A', 
            
            // ✨ PERBAIKAN KRUSIAL DI SINI:
            image: p.foto_url // Menggunakan field foto_url dari Laravel Resource
                ? `${LARAVEL_BASE_URL}/storage/${p.foto_url}` // Gabungkan URL base + /storage/ + path dari DB
                : 'https://source.unsplash.com/random/400x400/?laptop,charger', // Fallback
            // -----------------------------
        }));

    } catch (err: any) {
        console.error("Gagal mengambil data produk:", err);
        error.value = "Gagal memuat produk. Cek koneksi server Laravel (port 8000).";
    } finally {
        isLoading.value = false;
    }
};

// Panggil fungsi saat komponen selesai dimuat
onMounted(fetchProducts);


// Fungsi helper untuk format harga
const formatPrice = (price: number) => {
    // Pastikan harga adalah angka
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return 'Rp 0';
    return `Rp ${numericPrice.toLocaleString('id-ID')}`;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-sans">

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
        
        <main class="max-w-7xl mx-auto px-6 py-6">

            <div class="bg-white p-4 rounded-xl shadow-md mb-8">
                <div class="h-64 rounded-xl flex items-center justify-between p-8 overflow-hidden relative">
                    
                    <img :src="heroImage" alt="Promo Hero Banner" class="absolute inset-0 w-full h-full object-cover rounded-xl"/>

                    </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Telusuri Kategori Pilihan</h3>
                <div class="grid grid-cols-4 sm:grid-cols-8 gap-4 text-center">
                    <div v-for="category in categories" :key="category.name" class="flex flex-col items-center p-2 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                        <span class="text-3xl mb-1">{{ category.icon }}</span>
                        <span class="text-xs text-gray-700 font-medium">{{ category.name }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-20">
                <h3 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <span class="text-purple-600">Untuk Anda</span>
                </h3>

                <div v-if="isLoading" class="text-center py-10 text-gray-500">
                    <p>Mengambil data produk dari Laravel...</p>
                    <svg class="animate-spin h-5 w-5 mx-auto mt-4 text-purple-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div v-else-if="error" class="text-center py-10 text-red-500 border border-red-200 p-4 rounded-lg">
                    <p>{{ error }}</p>
                    <p class="text-sm text-red-400 mt-1">Pastikan server Laravel berjalan di **http://127.0.0.1:8000** dan routing `/api/produks` sudah benar.</p>
                </div>

                <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <router-link
                      v-for="product in products"
                      :key="product.id"
                      :to="{ name: 'product-detail', params: { id: product.id } }"
                      class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 cursor-pointer overflow-hidden block"
                    >
                        <div class="w-full aspect-square overflow-hidden">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
                        </div>

                        <div class="p-3">
                            <p class="text-sm font-medium text-gray-800 line-clamp-2 mb-1">{{ product.name }}</p>
                            
                            <p class="text-md font-bold text-red-600 mb-2">{{ formatPrice(product.price) }}</p>

                            <div class="flex items-center text-xs text-gray-500">
                                <span class="text-yellow-400 mr-1">★</span>
                                <span>{{ product.rating }}</span>
                                <span class="mx-1">•</span>
                                <span>{{ product.sellerCity }}</span>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </main>
        
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

    </div>
</template>