import { describe, it, expect, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import ProductDetail from './ProductDetail.vue'

vi.mock('vue-router', () => ({
    useRoute: () => ({
        params: {
            id: '1',
        },
    }),
    RouterLink: {
        template: '<a><slot /></a>',
    },
}))

vi.mock('@/stores', () => ({
    useProdukStore: () => ({
        error: null,
        fetchPublicProdukById: vi.fn().mockResolvedValue({
            id: 1,
            namaProduk: 'Laptop Lenovo',
            harga: 5000000,
            deskripsi: 'Laptop ini cocok untuk kebutuhan mahasiswa.',
            kondisi: 'Baru',
            stok: 10,
            fotoProduk: 'https://example.com/laptop.jpg',
            kategori: {
                nama_kategori: 'Elektronik',
            },
            penjual: {
                nama_toko: 'Toko Mahasiswa',
                lokasi: 'Semarang',
            },
            reviews: [
                {
                    id: 1,
                    namaPengunjung: 'Nina',
                    emailPengunjung: 'nina@example.com',
                    provinsiPengunjung: 'Jawa Tengah',
                    rating: 5,
                    ulasan: 'Produknya bagus dan sesuai deskripsi.',
                    created_at: '2026-06-05T00:00:00.000Z',
                },
            ],
        }),
    }),
    useReviewStore: () => ({
        error: null,
        createReview: vi.fn(),
    }),
}))

describe('ProductDetail.vue - Unit Testing Tab Produk', () => {
    it('DUPL-18-01: menampilkan daftar ulasan ketika tab Ulasan Pembeli diklik', async () => {
        const wrapper = mount(ProductDetail, {
            global: {
                stubs: {
                    RouterLink: {
                        template: '<a><slot /></a>',
                    },
                },
            },
        })

        await flushPromises()

        const reviewTab = wrapper
            .findAll('button')
            .find((button) => button.text().includes('Ulasan Pembeli'))

        expect(reviewTab).toBeTruthy()

        await reviewTab!.trigger('click')

        expect(wrapper.text()).toContain('Nina')
        expect(wrapper.text()).toContain('nina@example.com')
        expect(wrapper.text()).toContain('Jawa Tengah')
        expect(wrapper.text()).toContain('5/5')
        expect(wrapper.text()).toContain('Produknya bagus dan sesuai deskripsi.')
    })

    it('DUPL-18-02: menampilkan deskripsi produk ketika tab Deskripsi Produk diklik', async () => {
        const wrapper = mount(ProductDetail, {
            global: {
                stubs: {
                    RouterLink: {
                        template: '<a><slot /></a>',
                    },
                },
            },
        })

        await flushPromises()

        const descriptionTab = wrapper
            .findAll('button')
            .find((button) => button.text().includes('Deskripsi Produk'))

        expect(descriptionTab).toBeTruthy()

        await descriptionTab!.trigger('click')

        expect(wrapper.text()).toContain('Laptop ini cocok untuk kebutuhan mahasiswa.')
    })
})