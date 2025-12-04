# Testing Upload Gambar ke Supabase

Gunakan Postman atau Thunder Client untuk test upload:

## Endpoint
```
POST http://127.0.0.1:8000/api/penjual/produks/upload-gambar
```

## Headers
```
Authorization: Bearer {your-penjual-token}
```

## Body (form-data)
```
gambar: [pilih file jpg/png/jpeg, max 2MB]
```

## Expected Response (Success)
```json
{
    "message": "Gambar berhasil diupload",
    "path": "produks/1733318400_abc123.jpg",
    "url": "https://luvojjczbzbduplwvjtg.supabase.co/storage/v1/object/public/nexel-storage/produks/1733318400_abc123.jpg"
}
```

Setelah upload berhasil, update produk dengan path tersebut:

```
PUT http://127.0.0.1:8000/api/penjual/produks/{id}

Body:
{
    "fotoProduk": "produks/1733318400_abc123.jpg"
}
```

Gambar akan langsung tampil di frontend!
