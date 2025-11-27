<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const showModal = ref(false);
const modalMode = ref('add'); // 'add' atau 'edit'
const products = ref([]);

const formData = ref({
  id: null,
  nama: '',
  kategori: '',
  harga: '',
  stok: '',
  deskripsi: '',
  gambar: ''
});

const searchQuery = ref('');

onMounted(() => {
  loadProducts();
});

const loadProducts = () => {
  // TODO: Fetch dari API backend
  // Sementara data dummy
  products.value = [
    {
      id: 1,
      nama: 'Laptop Gaming ASUS ROG',
      kategori: 'Elektronik',
      harga: 15000000,
      stok: 10,
      deskripsi: 'Laptop gaming dengan performa tinggi',
      gambar: 'https://via.placeholder.com/150'
    },
    {
      id: 2,
      nama: 'Mouse Wireless Logitech',
      kategori: 'Aksesoris',
      harga: 250000,
      stok: 50,
      deskripsi: 'Mouse wireless dengan koneksi stabil',
      gambar: 'https://via.placeholder.com/150'
    }
  ];
};

const openAddModal = () => {
  modalMode.value = 'add';
  resetForm();
  showModal.value = true;
};

const openEditModal = (product) => {
  modalMode.value = 'edit';
  formData.value = { ...product };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  formData.value = {
    id: null,
    nama: '',
    kategori: '',
    harga: '',
    stok: '',
    deskripsi: '',
    gambar: ''
  };
};

const handleSubmit = () => {
  if (modalMode.value === 'add') {
    // TODO: POST ke API
    const newProduct = {
      ...formData.value,
      id: products.value.length + 1,
      gambar: 'https://via.placeholder.com/150'
    };
    products.value.push(newProduct);
  } else {
    // TODO: PUT ke API
    const index = products.value.findIndex(p => p.id === formData.value.id);
    if (index !== -1) {
      products.value[index] = { ...formData.value };
    }
  }
  closeModal();
};

const deleteProduct = (productId) => {
  if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
    // TODO: DELETE ke API
    products.value = products.value.filter(p => p.id !== productId);
  }
};

const formatRupiah = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
};

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value;
  return products.value.filter(product => 
    product.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    product.kategori.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-3xl font-bold text-gray-900">Kelola Produk</h2>
      <p class="text-gray-600 mt-2">Kelola semua produk yang Anda jual</p>
    </div>
    
    <!-- Action Bar -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari produk berdasarkan nama atau kategori..."
              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            />
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
        
        <button
          @click="openAddModal"
          class="flex items-center justify-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 shadow-md"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah Produk
        </button>
      </div>
    </div>
    
    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
              <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50 transition-colors duration-150">
              <td class="px-6 py-4 whitespace-nowrap">
                <img :src="product.gambar" :alt="product.nama" class="w-16 h-16 object-cover rounded-lg" />
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">{{ product.nama }}</div>
                <div class="text-sm text-gray-500 line-clamp-1">{{ product.deskripsi }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                  {{ product.kategori }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                {{ formatRupiah(product.harga) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                  product.stok > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ product.stok }} unit
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center gap-2">
                  <button
                    @click="openEditModal(product)"
                    class="text-purple-600 hover:text-purple-900 transition-colors duration-150"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteProduct(product.id)"
                    class="text-red-600 hover:text-red-900 transition-colors duration-150"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            
            <tr v-if="filteredProducts.length === 0">
              <td colspan="6" class="px-6 py-12 text-center">
                <div class="text-gray-500">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                  </svg>
                  <p class="text-lg">Belum ada produk</p>
                  <p class="text-sm mt-1">Mulai tambahkan produk Anda</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="handleSubmit">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="mb-4">
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                  {{ modalMode === 'add' ? 'Tambah Produk Baru' : 'Edit Produk' }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">{{ modalMode === 'add' ? 'Isi formulir untuk menambahkan produk baru' : 'Ubah informasi produk' }}</p>
              </div>
              
              <div class="space-y-4">
                <div>
                  <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                  <input
                    v-model="formData.nama"
                    type="text"
                    id="nama"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Masukkan nama produk"
                  />
                </div>
                
                <div>
                  <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                  <input
                    v-model="formData.kategori"
                    type="text"
                    id="kategori"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Masukkan kategori"
                  />
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                    <input
                      v-model="formData.harga"
                      type="number"
                      id="harga"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      placeholder="0"
                    />
                  </div>
                  
                  <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input
                      v-model="formData.stok"
                      type="number"
                      id="stok"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      placeholder="0"
                    />
                  </div>
                </div>
                
                <div>
                  <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                  <textarea
                    v-model="formData.deskripsi"
                    id="deskripsi"
                    rows="3"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Masukkan deskripsi produk"
                  ></textarea>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
              >
                {{ modalMode === 'add' ? 'Tambah' : 'Simpan' }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors duration-200"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
  line-clamp: 1;
}
</style>