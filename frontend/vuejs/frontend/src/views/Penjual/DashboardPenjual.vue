<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useProdukStore, usePenjualStore, useReviewStore } from '@/stores';

// --- Imports untuk Chart.js ---
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js';
import { Bar, Doughnut } from 'vue-chartjs';

// --- Import jsPDF ---
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

// Registrasi komponen Chart.js
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const router = useRouter();
const route = useRoute();
const activeMenu = ref('home');
const produkStore = useProdukStore();
const penjualStore = usePenjualStore();
const reviewStore = useReviewStore();

const getRating = (product) => {
  const val = product.rating || product.reviews_avg_rating || 0;
  return parseFloat(val) || 0;
};

const getKategoriName = (kategori) => {
  if (!kategori) return '-';
  if (typeof kategori === 'object') {
    return kategori.namaKategori || kategori.nama || kategori.name || '-';
  }
  return String(kategori);
};

// ... (Kode Computed Properties Lama Tetap Sama) ...
const namaPenjual = computed(() => {
  if (penjualStore.profile?.namaPenjual) {
    return penjualStore.profile.namaPenjual;
  }
  return localStorage.getItem('nama_penjual') || 'Penjual';
});

const stats = computed(() => penjualStore.dashboardStats || {});

const totalProduk = computed(() => stats.value.total_produk || produkStore.produkList.length || 0);
const totalPenjualan = computed(() => stats.value.total_penjualan || 0);
const totalUlasan = computed(() => stats.value.total_ulasan || 0);
const ulasanBaru = computed(() => stats.value.ulasan_baru || 0);
const aktivitasTerbaru = computed(() => stats.value.aktivitas_terbaru || []);

const isLoading = computed(() => {
  return penjualStore.isLoading || produkStore.isLoading || reviewStore.isLoading;
});

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(angka);
};

// ... (Kode Chart Data Lama Tetap Sama) ...
const stockChartData = computed(() => {
  const products = produkStore.produkList.slice(0, 10); 
  return {
    labels: products.map(p => p.namaProduk), 
    datasets: [{
      label: 'Jumlah Stok',
      backgroundColor: '#9333ea', 
      data: products.map(p => p.stok), 
      borderRadius: 4
    }]
  };
});

const ratingChartData = computed(() => {
  const products = produkStore.produkList.slice(0, 10);
  return {
    labels: products.map(p => p.namaProduk), // Pastikan ini p.namaProduk bukan p.nama_produk
    datasets: [{
      label: 'Rata-rata Rating',
      backgroundColor: '#fbbf24', 
      data: products.map(p => p.rating || 0), 
      borderRadius: 4
    }]
  };
});

// Di dalam DashboardPenjual.vue

const locationChartData = computed(() => {
  // Data dari backend formatnya: { "Jawa Barat": 10, "Bali": 5 }
  const dataLokasi = stats.value.sebaran_lokasi || {};
  
  const labels = Object.keys(dataLokasi);
  const data = Object.values(dataLokasi);

  return {
    labels: labels.length ? labels : ['Belum ada data'],
    datasets: [{
      backgroundColor: ['#4ade80', '#60a5fa', '#f87171', '#a78bfa', '#fbbf24', '#cbd5e1'],
      data: data.length ? data : [1] // Dummy 1 agar chart muncul (abu-abu) kalau kosong
    }]
  };
});

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: { y: { beginAtZero: true } }
};

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { position: 'right' } }
};

// --- LOGIKA EXPORT PDF ---
const generatePDF = (type) => {
  const doc = new jsPDF();
  const products = [...produkStore.produkList]; // Clone array agar tidak mengubah state asli

  let title = '';
  let head = [];
  let body = [];

  // Format tanggal hari ini
  const today = new Date().toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });

  if (type === 'stok_desc') {
    // 1. Laporan Stok (Urut Stok Menurun)
    title = 'Laporan Stok Produk (Tertinggi ke Terendah)';
    
    // Sorting by stok desc
    products.sort((a, b) => b.stok - a.stok);

    head = [['No', 'Nama Produk', 'Kategori', 'Harga', 'Rating', 'Stok']];
    body = products.map((p, index) => [
      index + 1,
      p.namaProduk,
      getKategoriName(p.kategori),
      formatRupiah(p.harga),
      getRating(p).toFixed(1),
      p.stok
    ]);

  } else if (type === 'rating_desc') {
    // 2. Laporan Rating (Urut Rating Menurun)
    title = 'Laporan Rating Produk (Tertinggi ke Terendah)';
    
    // Sorting by rating desc
    products.sort((a, b) => (b.rating || 0) - (a.rating || 0));

    head = [['No', 'Nama Produk', 'Kategori', 'Harga', 'Stok', 'Rating']];
    body = products.map((p, index) => [
      index + 1,
      p.namaProduk,
      getKategoriName(p.kategori),
      formatRupiah(p.harga),
      p.stok,
      getRating(p).toFixed(1)
    ]);

  } else if (type === 'urgent_stock') {
    // 3. Laporan Restock (Stok < 2)
    title = 'Laporan Barang Harus Segera Dipesan (Stok < 2)';
    
    // Filter stok < 2
    const urgentProducts = products.filter(p => p.stok < 2);
    
    // Sorting by stok asc (biar yang 0 paling atas)
    urgentProducts.sort((a, b) => a.stok - b.stok);

    head = [['No', 'Nama Produk', 'Kategori', 'Harga', 'Sisa Stok']];
    body = urgentProducts.map((p, index) => [
      index + 1,
      p.namaProduk,
      getKategoriName(p.kategori),
      formatRupiah(p.harga),
      p.stok
    ]);
  }

  // Header Dokumen
  doc.setFontSize(18);
  doc.text('NEXEL - Laporan Penjualan', 14, 20);
  doc.setFontSize(12);
  doc.text(title, 14, 30);
  doc.setFontSize(10);
  doc.text(`Dicetak Oleh: ${namaPenjual.value}`, 14, 38);
  doc.text(`Tanggal: ${today}`, 14, 43);

  // Tabel
  autoTable(doc, {
    startY: 50,
    head: head,
    body: body,
    theme: 'grid',
    headStyles: { fillColor: [147, 51, 234] }, // Warna Ungu NEXEL
  });

  // Save PDF
  doc.save(`${type}_${new Date().getTime()}.pdf`);
};

// ... (OnMounted dan fungsi navigasi lainnya tetap sama) ...
onMounted(async () => {
  if (route.path.includes('kelolaproduk')) {
    activeMenu.value = 'produk';
  } else {
    activeMenu.value = 'home';
  }
  
  const token = localStorage.getItem('token');
  if (!token) {
    router.push('/login');
    return;
  }
  
  try {
    await Promise.all([
      penjualStore.fetchProfile().catch(() => {}),
      penjualStore.fetchDashboardStats().catch(() => {}),
      produkStore.fetchAllProduk().catch(() => {}),
      reviewStore.fetchReviewsBySeller ? reviewStore.fetchReviewsBySeller() : Promise.resolve([]) 
    ]);
  } catch (error) {
    console.error('Error loading dashboard data:', error);
  }
});

const navigateTo = (menu, path) => {
  activeMenu.value = menu;
  router.push(path);
};

const handleLogout = async () => {
  await penjualStore.logout();
  router.push('/login');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <aside class="w-64 bg-white shadow-lg fixed h-full z-10">
      <div class="p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-purple-600">NEXEL</h1>
        <p class="text-sm text-gray-500 mt-1">Dashboard Penjual</p>
      </div>
      <nav class="p-4">
        <ul class="space-y-2">
          <li>
            <button @click="navigateTo('home', '/dashboard-penjual')" :class="['w-full flex items-center px-4 py-3 rounded-lg text-left transition-all duration-200', activeMenu === 'home' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-purple-50 hover:text-purple-600']">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
              <span class="font-medium">Home</span>
            </button>
          </li>
          <li>
            <button @click="navigateTo('produk', '/dashboard-penjual/kelolaproduk')" :class="['w-full flex items-center px-4 py-3 rounded-lg text-left transition-all duration-200', activeMenu === 'produk' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-700 hover:bg-purple-50 hover:text-purple-600']">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
              <span class="font-medium">Produk</span>
            </button>
          </li>
        </ul>
      </nav>
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200">
        <button @click="handleLogout" class="w-full flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
          <span class="font-medium">Logout</span>
        </button>
      </div>
    </aside>
    
    <main class="ml-64 flex-1 p-8">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" v-if="Component" />
          <div v-else>
            <div v-if="isLoading" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
            </div>
            
            <div v-else>
              <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                  <h2 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ namaPenjual }}! 👋</h2>
                  <p class="text-gray-600 mt-2">Selamat datang di dashboard penjual NEXEL</p>
                </div>

                <div class="flex flex-wrap gap-2">
                  <button 
                    @click="generatePDF('stok_desc')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center text-sm shadow-sm"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Laporan Stok
                  </button>
                  <button 
                    @click="generatePDF('rating_desc')" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center text-sm shadow-sm"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Laporan Rating
                  </button>
                  <button 
                    @click="generatePDF('urgent_stock')" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center text-sm shadow-sm"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Stok Menipis
                  </button>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-600">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Total Produk</p>
                      <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalProduk }}</p>
                      <p class="text-xs text-gray-500 mt-1">Produk aktif</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                      <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                  </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-gray-600 font-medium">Total Ulasan</p>
                      <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalUlasan }}</p>
                      <p class="text-xs text-gray-500 mt-1"><span class="text-yellow-600 font-semibold">{{ ulasanBaru }}</span> ulasan baru (7 hari)</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                      <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </div>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                   <h3 class="text-lg font-bold text-gray-800 mb-4">Sebaran Stok Produk</h3>
                   <div class="h-64"><Bar :data="stockChartData" :options="barChartOptions" /></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                   <h3 class="text-lg font-bold text-gray-800 mb-4">Rating per Produk</h3>
                   <div class="h-64"><Bar :data="ratingChartData" :options="barChartOptions" /></div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                   <h3 class="text-lg font-bold text-gray-800 mb-4">Sebaran Lokasi Reviewer</h3>
                   <div class="h-64 flex justify-center"><Doughnut :data="locationChartData" :options="doughnutChartOptions" /></div>
                </div>
              </div>

              <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Aktivitas Terbaru</h3>
                <div v-if="aktivitasTerbaru.length > 0" class="space-y-3">
                  <div v-for="(aktivitas, index) in aktivitasTerbaru" :key="index" class="flex items-start p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <div class="ml-4 flex-1">
                      <p class="text-sm font-medium text-gray-900">{{ aktivitas.judul }}</p>
                      <p class="text-sm text-gray-600 mt-1">{{ aktivitas.deskripsi }}</p>
                      <p class="text-xs text-gray-500 mt-2">{{ aktivitas.waktu }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                  <p class="font-medium">Belum ada aktivitas</p>
                </div>
              </div>

            </div>
          </div>
        </transition>
      </router-view>
    </main>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>