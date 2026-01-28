# Database Schema Structure

Berikut adalah struktur tabel database untuk aplikasi E-Commerce Antigravity/Fensacel, berdasarkan file migrasi yang telah dibuat.

## 1. Users (`users`)
Menyimpan data pengguna (Admin & Customer).
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID User |
| `name` | String | | Nama Lengkap |
| `email` | String | Unique | Email User |
| `password` | String | | Hashed Password |
| `role` | Enum/String | Default: 'user' | 'admin' atau 'user' |
| `timestamps` | Timestamp | | created_at, updated_at |

## 2. Categories (`categories`)
**[BARU]** Kategori untuk mengelompokkan produk.
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Kategori |
| `name` | String | | Nama Kategori |
| `slug` | String | Unique | URL Friendly Name |
| `image` | String | Nullable | URL Gambar Icon Kategori |
| `is_active` | Boolean | Default: true | Status Aktif |
| `timestamps` | Timestamp | | created_at, updated_at |

## 3. Products (`products`)
Menyimpan data produk utama.
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Produk |
| `category_id` | BigInt | FK, Nullable | Relasi ke `categories` **[BARU]** |
| `name` | String | | Nama Produk |
| `slug` | String | Unique | URL Friendly Name |
| `description` | Text | Nullable | Deskripsi Produk |
| `price` | Decimal | 10,2 | Harga Dasar |
| `image` | String | Nullable | URL Gambar Utama |
| `stock` | Integer | Default: 0 | Stok Produk |
| `discount_price` | Decimal | Nullable | Harga Coret/Diskon (jika ada) |
| `is_active` | Boolean | Default: true | Status Tayang |
| `deleted_at` | Timestamp | Nullable | Soft Deletes |
| `timestamps` | Timestamp | | created_at, updated_at |

## 4. Product Packages (`product_packages`)
Varian paket harga dalam satu produk (opsional per produk).
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Paket |
| `product_id` | BigInt | FK | Relasi ke `products` |
| `name` | String | | Nama Paket (misal: "Paket Hemat") |
| `price` | Decimal | 15,2 | Harga Paket |
| `description` | Text | Nullable | List Fitur/Deskripsi Paket |
| `timestamps` | Timestamp | | created_at, updated_at |

## 5. Orders (`orders`)
Menyimpan transaksi pemesanan.
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Order |
| `user_id` | BigInt | FK, Nullable | Relasi ke `users` (jika login) |
| `order_number` | String | Unique | No Resi/Order ID (e.g. ORD-123) |
| `total_price` | Decimal | 15,2 | Total Bayar |
| `status` | String | Default: 'pending' | pending, success, failed, cancelled |
| `payment_type` | String | Nullable | e.g. 'bank_transfer', 'gopay' |
| `payment_info` | JSON | Nullable | Detail VA, QR Code, dll |
| `snap_token` | String | Nullable | Token Midtrans |
| `customer_name` | String | | Nama Pemesan |
| `customer_email` | String | Nullable | Email Pemesan **[BARU]** |
| `customer_phone` | String | Nullable | No HP Pemesan |
| `promo_code_id` | BigInt | FK, Nullable | Relasi ke `promo_codes` |
| `promo_discount` | Decimal | Default: 0 | Nominal Potongan Promo |
| `timestamps` | Timestamp | | created_at, updated_at |

## 6. Order Items (`order_items`)
Detail barang yang dibeli dalam satu order.
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Item |
| `order_id` | BigInt | FK | Relasi ke `orders` |
| `product_id` | BigInt | FK, Nullable | Relasi ke `products` |
| `product_name` | String | | Snapshot Nama Produk (History) |
| `package_name` | String | Nullable | Snapshot Nama Paket (History) |
| `quantity` | Integer | | Jumlah Beli |
| `price` | Decimal | 15,2 | Harga Satuan saat dibeli |
| `timestamps` | Timestamp | | created_at, updated_at |

## 7. Promo Codes (`promo_codes`)
Kode voucher diskon.
| Column | Type | Attributes | Description |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | PK, Auto Increment | ID Promo |
| `code` | String | Unique | Kode Unik (e.g. "DISKON50") |
| `discount_amount` | Decimal | | Nilai Diskon |
| `type` | Enum | fixed/percent | Tipe Potongan |
| `usage_limit` | Integer | Nullable | Batas Total Penggunaan |
| `used_count` | Integer | Default: 0 | Jumlah Terpakai |
| `valid_until` | DateTime | Nullable | Tanggal Kadaluarsa |
| `timestamps` | Timestamp | | created_at, updated_at |

---

### Catatan Tambahan
1.  **Categories**: Tabel baru `categories` telah ditambahkan.
2.  **Order Email**: Kolom `customer_email` telah ditambahkan ke tabel `orders` untuk kebutuhan notifikasi pembayaran.
3.  **Packages**: Tabel `product_packages` digunakan untuk variasi harga produk.
4.  **Snap Token**: Disimpan di `orders` untuk resume pembayaran jika user menutup popup.
