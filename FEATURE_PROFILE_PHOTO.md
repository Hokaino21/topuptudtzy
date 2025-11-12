# Fitur Upload Foto Profil - Dokumentasi

## ✅ Fitur yang Telah Diimplementasikan

Saya telah berhasil menambahkan fitur upload foto profil yang berfungsi sepenuhnya dari halaman edit profil hingga ditampilkan di dashboard. Berikut adalah detail implementasinya:

### 1. **Database Migration**
**File:** `database/migrations/2025_11_12_add_profile_photo_to_users_table.php`

- Menambahkan kolom `profile_photo_path` (nullable) ke tabel `users`
- Kolom ini menyimpan path relatif dari foto profil yang diupload

### 2. **User Model**
**File:** `app/Models/User.php`

**Perubahan:**
- Menambahkan `'profile_photo_path'` ke array `$fillable` untuk memungkinkan mass assignment
- Menambahkan accessor `getProfilePhotoUrlAttribute()` yang:
  - Mengembalikan URL ke foto profil jika ada
  - Mengembalikan avatar placeholder dari `ui-avatars.com` jika belum ada foto
  - Format: `https://ui-avatars.com/api/?name=[nama]&color=7c3aed&background=1e293b&rounded=true`

### 3. **Form Request Validation**
**File:** `app/Http/Requests/ProfileUpdateRequest.php`

**Rules untuk `profile_photo`:**
```php
'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
```

- **nullable**: Foto profil tidak wajib (opsional)
- **image**: File harus berupa gambar
- **mimes:jpeg,png,jpg,gif**: Hanya format JPEG, PNG, GIF yang diizinkan
- **max:2048**: Ukuran maksimal 2MB

### 4. **Profile Controller**
**File:** `app/Http/Controllers/ProfileController.php`

**Logika Update:**
```php
// Handle profile photo upload
if ($request->hasFile('profile_photo')) {
    // Hapus foto lama jika ada
    if ($user->profile_photo_path) {
        Storage::disk('public')->delete($user->profile_photo_path);
    }
    
    // Simpan foto baru
    $path = $request->file('profile_photo')->store('profile-photos', 'public');
    $validated['profile_photo_path'] = $path;
}
```

- Cek apakah ada file yang diupload
- Hapus foto lama (jika ada) dari storage untuk menghemat space
- Simpan foto baru ke folder `storage/app/public/profile-photos/`
- Simpan path ke database

### 5. **Edit Profil Form**
**File:** `resources/views/profile/partials/update-profile-information-form.blade.php`

**Fitur:**
- Form multipart untuk upload file
- Input file dengan accept filter untuk gambar saja
- **Live Preview:** Menggunakan Alpine.js untuk preview foto sebelum upload
- Tampilan foto profil saat ini dengan fallback placeholder
- Validasi error display
- Instruksi untuk user tentang format dan ukuran file

**HTML Structure:**
```html
<input type="file" 
       id="profile_photo" 
       name="profile_photo" 
       accept="image/*"
       @change="// Handle preview"
/>
```

### 6. **Dashboard**
**File:** `resources/views/dashboard.blade.php`

**User Profile Card:**
- Menampilkan foto profil menggunakan `{{ Auth::user()->profile_photo_url }}`
- Mengganti avatar placeholder dengan foto profil yang actual
- Foto ditampilkan di komponen user profile card di sidebar kanan

---

## 🚀 Cara Menggunakan

### Untuk Upload Foto Profil:

1. **Login** ke aplikasi
2. **Klik menu Profil** (biasanya di header atau dropdown user)
3. **Ke halaman Edit Profile**
4. **Di bagian "Profile Photo":**
   - Upload foto dengan format JPEG, PNG, atau GIF
   - Ukuran maksimal 2MB
   - Preview akan muncul sebelum disimpan
5. **Klik tombol "Save"** untuk menyimpan profil beserta foto

### Foto Profil akan Ditampilkan di:
- ✅ **Dashboard** - di User Profile Card (sidebar kanan)
- ✅ **Edit Profil** - sebagai preview
- ✅ **Halaman apapun yang menggunakan `Auth::user()->profile_photo_url`**

---

## 📁 File Storage

Foto profil disimpan di:
- **Local Path:** `storage/app/public/profile-photos/[nama-file-random]`
- **Public URL:** `http://yourdomain/storage/profile-photos/[nama-file-random]`
- **Symlink:** Pastikan sudah membuat symlink dengan: `php artisan storage:link`

---

## 🔧 Konfigurasi

### Setup Symlink (jika belum ada):
```bash
php artisan storage:link
```

Perintah ini akan membuat symlink dari `public/storage` → `storage/app/public`

### Config Filesystem:
File `.env` atau `config/filesystems.php` sudah dikonfigurasi untuk menggunakan disk `public`

---

## ⚙️ Database Schema

```sql
ALTER TABLE users ADD COLUMN profile_photo_path VARCHAR(255) NULLABLE AFTER email_verified_at;
```

**Kolom:**
- `profile_photo_path` (string, nullable) - Menyimpan path relatif dari foto

---

## 🧪 Testing Fitur

### Test Case 1: Upload Foto Baru
1. Login dengan user baru (belum ada foto)
2. Go to Edit Profile
3. Upload foto
4. Klik Save
5. Verify foto muncul di dashboard dan form

### Test Case 2: Update Foto Existing
1. Login dengan user yang sudah punya foto
2. Go to Edit Profile
3. Upload foto baru
4. Klik Save
5. Verify foto lama dihapus dan foto baru muncul

### Test Case 3: Validasi
1. Coba upload file > 2MB → Error message
2. Coba upload file non-image (txt, pdf) → Error message
3. Coba skip upload → Foto tidak berubah (opsional)

---

## 📝 Catatan Penting

1. **Alpine.js** sudah ada di project (untuk live preview)
2. **Storage Link** harus aktif agar foto bisa diakses publik
3. **Placeholder Avatar** otomatis generate dari UI Avatars jika belum ada foto
4. **Foto Lama** otomatis dihapus saat upload yang baru
5. **Validasi Server-side** mencegah upload file berbahaya

---

## 🐛 Troubleshooting

### Foto tidak muncul di dashboard?
- Pastikan symlink sudah dibuat: `php artisan storage:link`
- Cek folder `storage/app/public/profile-photos/` sudah ada

### Error "File too large"?
- Tingkatkan `max` di konfigurasi upload atau di validation rules

### Placeholder muncul terus?
- Cek database, pastikan `profile_photo_path` ter-save
- Cek di browser console untuk JS errors

---

**Status: ✅ SELESAI - Fitur siap digunakan!**
