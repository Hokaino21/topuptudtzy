# 📸 VISUAL GUIDE - FITUR UPLOAD FOTO PROFIL

## 1. Edit Profil Form (update-profile-information-form.blade.php)

```
┌─────────────────────────────────────────────────────┐
│ Profile Information                                 │
│                                                     │
│ Update your account's profile information and      │
│ email address.                                      │
└─────────────────────────────────────────────────────┘

📷 Profile Photo
   Upload a photo (JPEG, PNG, GIF - max 2MB)

   ┌──────────────┐    
   │              │  ┌─────────────────────────┐
   │   [PREVIEW]  │  │ [Browse...] [Choose...] │
   │              │  └─────────────────────────┘
   └──────────────┘  

   👤 Name
   ┌─────────────────────────────────────┐
   │ John Doe                            │
   └─────────────────────────────────────┘

   ✉️ Email
   ┌─────────────────────────────────────┐
   │ john@example.com                    │
   └─────────────────────────────────────┘

   ┌─────────────┐
   │    SAVE     │    Saved. ✓
   └─────────────┘
```

---

## 2. Live Preview Feature

### Sebelum Upload:
```
No Photo Uploaded Yet

┌──────────────┐
│      📷      │  <- Placeholder icon
│              │
└──────────────┘
```

### Saat Memilih File (Live Preview):
```
Photo Selected

┌──────────────┐
│              │
│   [ACTUAL    │  <- Real preview dari file
│    PHOTO]    │
│              │
└──────────────┘
```

### Setelah Upload & Save:
```
Photo Uploaded & Saved

┌──────────────┐
│              │
│   [PHOTO]    │  <- Stored & persisted
│              │
└──────────────┘
```

---

## 3. Dashboard Display

### User Profile Card (Before Upload)

```
┌────────────────────────────┐
│  Akun                      │
│  ┌─────────────┐           │
│  │   👤       │ Player     │  <- Placeholder Avatar
│  │             │ ID: #0001  │     Generated dari nama
│  └─────────────┘           │
│                            │
│  ┌──────────────────────┐  │
│  │  💰 Isi Saldo       │  │
│  └──────────────────────┘  │
└────────────────────────────┘
```

### User Profile Card (After Upload)

```
┌────────────────────────────┐
│  Akun                      │
│  ┌─────────────┐           │
│  │   [PHOTO]   │ John Doe  │  <- Real Photo
│  │             │ ID: #0001  │     Uploaded
│  └─────────────┘           │
│                            │
│  ┌──────────────────────┐  │
│  │  💰 Isi Saldo       │  │
│  └──────────────────────┘  │
└────────────────────────────┘
```

---

## 4. File Upload Flow

```
START
  ↓
User Click "Browse"
  ↓
Select Image File (JPEG/PNG/GIF ≤ 2MB)
  ↓
Preview Generates (Alpine.js)
  ↓
User Click "SAVE"
  ↓
Form Submit (multipart/form-data)
  ↓
SERVER SIDE ━━━━━━━━━━━━━━━━━━━━━━━━━
  ├─ Validate File
  │   ├─ Check type: image/jpeg, image/png, image/gif
  │   ├─ Check size: ≤ 2048 KB
  │   └─ Return error jika invalid
  │
  ├─ Delete Old Photo
  │   └─ Storage::disk('public')->delete(old_path)
  │
  ├─ Store New Photo
  │   └─ Store to: storage/app/public/profile-photos/[UUID].[ext]
  │
  └─ Save to Database
      └─ users.profile_photo_path = 'profile-photos/[UUID].[ext]'
  
  └─ Redirect to profile.edit with status 'profile-updated'
  ↓
User See Success Message "Saved."
  ↓
User Go to Dashboard
  ↓
Photo Displayed in User Profile Card
  ↓
END
```

---

## 5. Database Schema

### BEFORE Migration
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    balance DECIMAL(10,2),
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### AFTER Migration
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    profile_photo_path VARCHAR(255) NULL,          ← NEW COLUMN
    password VARCHAR(255),
    balance DECIMAL(10,2),
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 6. Storage File System Structure

```
Project Root
│
├── public/
│   ├── index.php
│   ├── storage/  ←─────────┐ [SYMLINK]
│   │   └── profile-photos/
│   │       ├── uuid-1.jpg
│   │       ├── uuid-2.png
│   │       └── uuid-3.gif
│   │
│   └── ...other files...
│
└── storage/
    └── app/
        └── public/
            └── profile-photos/  ←─ [ACTUAL STORAGE]
                ├── uuid-1.jpg
                ├── uuid-2.png
                └── uuid-3.gif
```

**How to Access:**
- Browser: `http://localhost/storage/profile-photos/uuid-1.jpg`
- Laravel: `asset('storage/profile-photos/uuid-1.jpg')`
- Blade: `{{ Auth::user()->profile_photo_url }}`

---

## 7. Validation Rules Breakdown

```
Input Field: profile_photo

┌────────────────────────────────────────────────┐
│ Validation Rules                               │
├────────────────────────────────────────────────┤
│ 1. nullable                                    │
│    ➜ Upload optional (tidak wajib)             │
│                                                │
│ 2. image                                       │
│    ➜ File harus image (GD, Imagick, etc)      │
│    ➜ Reject: .txt, .pdf, .exe, dll            │
│                                                │
│ 3. mimes:jpeg,png,jpg,gif                      │
│    ➜ Hanya format JPEG, PNG, GIF              │
│    ➜ Check MIME type (tidak hanya extension)  │
│                                                │
│ 4. max:2048                                    │
│    ➜ File size maximum 2048 KB (2 MB)         │
│    ➜ Reject jika lebih besar                   │
└────────────────────────────────────────────────┘
```

---

## 8. Error Messages

### Error Case 1: File Too Large
```
❌ The profile_photo field must be a file of at most 2048 kilobytes.
```

### Error Case 2: Invalid Format
```
❌ The profile_photo field must be a file of type: jpeg, png, jpg, gif.
```

### Error Case 3: Not an Image
```
❌ The profile_photo field must be an image.
```

---

## 9. Success Flow

```
✅ File Uploaded
   └─ Validation passed
      └─ Old file deleted (if exists)
         └─ New file stored to storage/
            └─ Path saved to database
               └─ Redirect with success message
                  └─ "Saved." appears for 2 seconds
                     └─ Photo visible in all pages
```

---

## 10. Compatibility

| Component | Status | Version |
|-----------|--------|---------|
| Laravel | ✅ | 11.x |
| PHP | ✅ | 8.1+ |
| MySQL | ✅ | 5.7+ |
| Alpine.js | ✅ | 3.x |
| Tailwind CSS | ✅ | 3.x |
| Storage | ✅ | Disk: public |
| Browser | ✅ | All modern browsers |

---

## 11. Testing Scenarios

### ✅ Valid Upload
```
File: photo.jpg (JPEG, 1.5MB)
Result: SUCCESS → Photo saved & displayed
```

### ❌ Invalid Format
```
File: document.pdf
Result: FAILED → Error message shown
```

### ❌ Too Large
```
File: bigphoto.jpg (5MB)
Result: FAILED → Error message shown
```

### ✅ Update Existing
```
Old Photo: exists
New Photo: upload
Result: SUCCESS → Old photo deleted, new photo saved
```

### ✅ No Photo
```
File: (skip upload)
Result: SUCCESS → Placeholder avatar shown
```

---

## 🎯 User Experience

### Desktop
```
1. Click input field
2. Browse from computer
3. Select image
4. See live preview
5. Click Save
6. See success message
7. Go to Dashboard
8. See photo in user card
```

### Mobile
```
1. Tap input field
2. Choose from Camera/Gallery
3. Select/Take photo
4. See live preview
5. Tap Save
6. See success message
7. Navigate to Dashboard
8. See photo in user card
```

---

**Last Updated: 2025-11-12**
