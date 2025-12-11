<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import { useProdukStore, useKategoriStore } from '@/stores';
import heroImage from '@/assets/hero.png';

// --- STORES ---
const produkStore = useProdukStore();
const kategoriStore = useKategoriStore();

// --- STATE DATA ---
const products = ref<any[]>([]); 
const activeCategory = ref<string | null>(null);
const categories = ref<any[]>([]);
const searchQuery = ref(''); 
const searchTimeout = ref<any>(null); 

// Computed untuk loading state dan error dari stores
const isLoading = computed(() => produkStore.isLoading || kategoriStore.isLoading);
const error = computed(() => produkStore.error || kategoriStore.error);


// Icon mapping untuk kategori
const getIcon = (categoryName: string): string => {
    const icons: Record<string, string> = {
        'Fashion': '👕',
        'Elektronik': '📱',
        'Makanan & Minuman': '🍔',
        'Kecantikan': '💅',
        'Rumah Tangga': '🛋️',
        'Olahraga': '⚽',
        'Otomotif': '🚗',
        'Hobi & Koleksi': '🎨',
        'Kesehatan': '🩺',
        'Laptop': '💻',
        'Charger': '🔌',
        'Aksesoris': '🎧',
    };
    return icons[categoryName] || '🏷️'; 
};

// Fetch categories dari store
const fetchCategories = async () => {
    try {
        console.log('🏷️ Fetching categories...');
        const apiCategories = await kategoriStore.fetchPublicKategoriList();
        
        console.log('📦 Raw categories from API:', apiCategories);
        
        if (apiCategories && apiCategories.length > 0) {
            categories.value = apiCategories.map((k: any) => ({
                name: k.namaKategori, // Konsisten dengan KategoriResource
                icon: getIcon(k.namaKategori)
            }));
            console.log('✅ Categories loaded:', categories.value.length, categories.value);
        } else {
            console.warn('⚠️ No categories returned from API');
        }
    } catch (err: any) {
        console.error("❌ Error loading categories:", err);
    }
};

// Fetch products dari store dengan filter
const fetchProducts = async () => {
    try {
        console.log('🔍 Fetching products...');
        
        // Fetch semua produk dari store (Asumsi ini mengambil SEMUA produk untuk filter klien)
        const apiProducts = await produkStore.fetchPublicProdukList();
        
        if (!apiProducts || apiProducts.length === 0) {
            console.warn('⚠️ No products returned from API');
            products.value = [];
            return;
        }

        console.log('📦 Total products from API:', apiProducts.length);

        // Apply filters
        let filteredProducts = [...apiProducts];

        // Filter by category
        if (activeCategory.value) {
            filteredProducts = filteredProducts.filter((p: any) => {
                const kategoriName = p.kategori?.namaKategori; // Konsisten dengan KategoriResource
                return kategoriName === activeCategory.value;
            });
            console.log(`🏷️ After category filter "${activeCategory.value}":`, filteredProducts.length);
        }

        // 🚀 REVISI PENCARIAN LENGKAP
        if (searchQuery.value.trim().length > 0) {
            const query = searchQuery.value.toLowerCase().trim();
            
            filteredProducts = filteredProducts.filter((p: any) => {
                const name = (p.namaProduk || '').toLowerCase();
                const desc = (p.deskripsi || '').toLowerCase();
                const kategoriName = (p.kategori?.namaKategori || '').toLowerCase(); // Kategori Produk
                const storeName = (p.penjual?.nama_toko || '').toLowerCase();        // Nama Toko
                // Asumsi key lokasi di objek penjual adalah kabupaten_kota dan propinsi
                const location = (p.penjual?.lokasi || '').toLowerCase(); // 🚨 Perubahan: Menggunakan KEY 'lokasi'

                return name.includes(query) || 
                       desc.includes(query) ||
                       kategoriName.includes(query) || // Cari di Nama Kategori
                       storeName.includes(query) ||    // Cari di Nama Toko
                       location.includes(query);
            });
            console.log(`🔎 After search filter "${searchQuery.value}":`, filteredProducts.length);
        }

        // Map to display format
        // (Tidak ada perubahan di sini, sudah benar)
        products.value = filteredProducts.map((p: any) => ({
            id: p.id,
            name: p.namaProduk, 
            price: p.harga, 
            rating: p.rating || 4.5, 
            totalReviews: p.totalReviews || 0, 
            nama_toko: p.penjual?.nama_toko || 'Toko Tidak Diketahui', 
            image: p.fotoProduk || 'https://via.placeholder.com/400x400?text=No+Image', 
        }));

        console.log('✅ Products ready for display:', products.value.length);

    } catch (err: any) {
        console.error("❌ Error fetching products:", err);
        products.value = [];
    }
};

// Live search dengan debounce
const performSearch = () => {
    clearTimeout(searchTimeout.value);
    
    searchTimeout.value = setTimeout(() => {
        fetchProducts();
    }, 300);
};

// Filter by category
const filterByCategory = (categoryName: string | null) => {
    searchQuery.value = '';
    activeCategory.value = categoryName;
    fetchProducts();
};

// Helper: Format harga ke Rupiah
const formatPrice = (price: number): string => {
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return 'Rp 0';
    return `Rp ${numericPrice.toLocaleString('id-ID')}`;
};

// Lifecycle: Load data saat component mounted
onMounted(async () => {
    console.log('🚀 HomeView mounted');
    await Promise.all([
        fetchProducts(),
        fetchCategories()
    ]);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 font-sans">

        <header class="bg-white shadow-md sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center gap-6">
                
                <h1 class="text-3xl font-extrabold text-purple-600 cursor-pointer pr-4">NEXEL</h1>
                
                <div class="flex-grow relative">
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="performSearch"
                        placeholder="Cari produk di Nexel..."
                        class="w-full py-3 pl-4 pr-12 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    />
                    <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex items-center gap-3 ml-4">
                    <router-link to="/login" class="px-4 py-2 border border-purple-600 text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition">
                        Masuk
                    </router-link>
                    <router-link to="/register" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">
                        Daftar
                    </router-link>
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
                    
                    <div 
                        @click="filterByCategory(null)" 
                        class="flex flex-col items-center p-2 rounded-lg cursor-pointer transition"
                        :class="{ 'bg-purple-100 text-purple-700 shadow-inner': activeCategory === null, 'hover:bg-gray-100': activeCategory !== null }"
                    >
                        <span class="text-3xl mb-1">🛒</span>
                        <span class="text-xs text-gray-700 font-medium" :class="{ 'text-purple-700 font-semibold': activeCategory === null }">Semua</span>
                    </div>

                    <div 
                        v-for="category in categories" 
                        :key="category.name" 
                        @click="filterByCategory(category.name)" 
                        class="flex flex-col items-center p-2 rounded-lg cursor-pointer transition"
                        :class="{ 'bg-purple-100 text-purple-700 shadow-inner': activeCategory === category.name, 'hover:bg-gray-100': activeCategory !== category.name }"
                    >
                        <span class="text-3xl mb-1">{{ category.icon }}</span>
                        <span class="text-xs text-gray-700 font-medium" :class="{ 'text-purple-700 font-semibold': activeCategory === category.name }">{{ category.name }}</span>
                    </div>
                    
                </div>
            </div>

            <div class="mb-20">
                <h3 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <span class="text-purple-600">
                        <span v-if="searchQuery.length > 0">Hasil Pencarian untuk: "{{ searchQuery }}"</span>
                        <span v-else>
                            {{ activeCategory ? activeCategory : 'Produk Terbaru' }}
                        </span>
                    </span>
                </h3>

                <div v-if="isLoading" class="text-center py-10 text-gray-500">
                    <p>{{ searchQuery.length > 0 ? 'Mencari produk...' : 'Memuat data produk...' }}</p>
                    <svg class="animate-spin h-5 w-5 mx-auto mt-4 text-purple-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div v-else-if="error" class="text-center py-10 border border-red-200 bg-red-50 p-6 rounded-lg">
                    <div class="text-red-600 mb-3">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-red-700 font-semibold mb-2">{{ error }}</p>
                    <button 
                        @click="fetchProducts()" 
                        class="mt-4 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                    >
                        🔄 Coba Lagi
                    </button>
                    <p class="text-xs text-red-500 mt-3">Pastikan Laravel server berjalan di http://127.0.0.1:8000</p>
                </div>

                <div v-else-if="products.length === 0" class="text-center py-10 text-gray-500 border border-dashed p-8 rounded-lg">
                    <p class="text-lg font-semibold mb-2">😢 Tidak Ada Produk Ditemukan.</p>
                    <p v-if="searchQuery.length > 0">Coba kata kunci lain atau periksa ejaan Anda.</p>
                    <p v-else>Tidak ada produk dalam kategori ini.</p>
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
                                <span>{{ product.nama_toko }}</span>
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