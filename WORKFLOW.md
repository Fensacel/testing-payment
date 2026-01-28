# Alur Kerja Sistem E-Commerce (Workflows)

Dokumen ini menjelaskan alur kerja utama (workflows) dalam aplikasi E-Commerce ini, mulai dari sisi User (Pelanggan), Admin, hingga Logika Sistem di balik layar.

## 1. Alur Belanja Pelanggan (User Flow)

### A. Penjelajahan Produk
1.  **Home Page**: User mendarat di halaman utama yang menampilkan *Hero Section*, *Kategori*, dan *Daftar Produk Unggulan*.
2.  **Detail Produk**: User mengklik produk untuk melihat foto, deskripsi, dan **Pilihan Paket** (jika ada varian paket).

### B. Keranjang & Checkout
1.  **Add to Cart**: Produk masuk ke keranjang. Stok divalidasi saat itu juga.
2.  **Keranjang Belanja**:
    *   User dapat mengubah jumlah (Quantity).
    *   User dapat mengubah tipe Paket (Package) langsung di keranjang.
    *   User **memilih (checklist)** item mana yang ingin dibayar (mendukung partial checkout).
3.  **Checkout Page**:
    *   Review barang yang akan dibayar.
    *   Input Informasi Pengiriman (Nama, No HP, Email).
    *   **Kode Promo**: User dapat memasukkan kode promo untuk mendapatkan diskon.

### C. Pembayaran (Payment)
1.  **Midtrans Snap**: Setelah klik "Bayar", popup pembayaran muncul (mendukung VA, QRIS, E-Wallet, Retail).
2.  **Status Pending**: Order terbentuk dengan status `Pending`. Stok produk otomatis **dikurangi** untuk mengamankan barang.
3.  **Konfirmasi**: User tidak perlu upload bukti transfer. Sistem otomatis mendeteksi pembayaran.

### D. Riwayat Pesanan
1.  **Order History**: User dapat melihat daftar pesanan.
2.  **Detail & Pembayaran Lanjutan**:
    *   Jika status *Pending*: User melihat **Kode Pembayaran / VA** atau **QR Code** langsung di halaman history.
    *   Jika status *Success*: Kode pembayaran disembunyikan, hanya tampil metode bayar.

---

## 2. Alur Backend & Logika Sistem (System Logic)

### A. Manajemen Stok Otomatis
*   **Saat Checkout**: Stok dikurangi (`decrement`).
*   **Saat Expired/Gagal**: Jika user tidak membayar dalam batas waktu Midtrans, atau membatalkan pesanan:
    *   Sistem menerima *Callback* dari Midtrans.
    *   Status order diupdate jadi `Failed` / `Cancelled`.
    *   **Stok dikembalikan (`increment`) otomatis** ke database agar bisa dibeli orang lain.

### B. Integrasi Payment Gateway (Midtrans)
*   **Snap Token**: Generated saat checkout untuk memunculkan popup bayar.
*   **Webhook/Callback**: Endpoint `/api/midtrans/notification` (atau handling di Controller) bertugas mendengarkan perubahan status pembayaran secara *real-time*.
*   **Data Capture**: Sistem menyimpan detail pembayaran (Bank apa, No VA berapa, Link QR) ke database untuk ditampilkan di history user/admin.

---

## 3. Alur Admin (Management Flow)

### A. Dashboard
*   Melihat ringkasan statistik: Total Order, Total Pendapatan, Jumlah Produk, dan User Terdaftar.
*   Melihat daftar **Recent Orders** (Pesanan Terbaru).

### B. Manajemen Produk
*   **CRUD Produk**: Admin bisa Tambah, Edit, Hapus produk.
*   **Paket Harga**: Admin bisa mengatur variasi harga (misal: "Paket Hemat", "Paket Premium") dalam satu produk.
*   **Toggle Status**: Mematikan/menghidupkan produk (Active/Inactive) tanpa menghapus data.

### C. Manajemen Order
*   **Monitoring**: Melihat semua pesanan masuk.
*   **Detail Pesanan**: Melihat siapa pembelinya, item apa yang dibeli, dan **Status Pembayaran**.
*   **Info Pembayaran**: Admin juga bisa melihat metode bayar dan kode bayar (jika customer bertanya).

### D. Fitur Tambahan
*   **Promo Codes**: Membuat kode diskon dengan limitasi (jumlah pakai, tanggal expired).
*   **User Management**: Mengelola daftar pengguna terdaftar.

---

## 4. Teknologi yang Digunakan
*   **Framework**: Laravel 11
*   **Database**: MySQL
*   **Frontend**: Blade Templates + Tailwind CSS (Custom compiled via Vite) + Alpine.js
*   **Payment**: Midtrans Payment Gateway
*   **Admin UI**: Custom Admin Panel (Tailwind Based)

---

## 5. Visualisasi Flowchart

Berikut adalah gambaran visual alur sistem menggunakan diagram:

```mermaid
graph TD
    %% Actors
    User([User / Customer])
    Admin([Admin])
    System[Sistem / Backend]
    Midtrans[Midtrans Gateway]
    DB[(Database)]

    %% User Flow
    subgraph "Shopping Process"
        User -->|1. Browse & Add to Cart| System
        System -->|Check Stock| DB
        User -->|2. Checkout & Input Data| System
        System -->|3. Create Order order_status=Pending| DB
        System -->|Decrement Stock| DB
        System -->|4. Request Snap Token| Midtrans
        Midtrans -->|Return Token| System
        System -->|Show Payment Popup| User
    end

    %% Payment Flow
    subgraph "Payment Logic"
        User -->|5. Pay via VA/QRIS/E-Wallet| Midtrans
        Midtrans -->|6. Webhook Notification| System
        System -->|7. Update Order Status payment_type & info| DB
        
        System -->{Status?}
        System -->|Settlement / Success| Success[Order Lunas]
        System -->|Expire / Cancel / Deny| Failed[Order Gagal]
        
        Failed -->|Increment Stock Refund| DB
    end

    %% Admin Flow
    subgraph "Admin Process"
        Admin -->|Login| System
        Admin -->|Manage Products & Stock| DB
        Admin -->|View Order History| DB
        Admin -->|View Payment Details| DB
    end
    
    %% Connections
    Success -->|Show in History| User
    Failed -->|Show in History| User
    Success -->|Show in Recent Orders| Admin
```
