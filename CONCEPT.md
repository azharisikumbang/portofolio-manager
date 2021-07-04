# Free Portofolio Manager

Free Portofolio Manager merupakan sebuah tool yang digunakan oleh developer / programmer / freelancer untuk mengelola portofolio pribadi miliknya, dengan tujuan memperkenalkan diri ke para calon client atau rekruter.

## Konsep Aplikasi

Konsep yang akan digunakan dalam tool ini sebagai berikut,

### Pengguna

Free Portofolio Manager memiliki,
1. Seorang administrator sebagai pengguna tunggal

### Data Pendukung Diri

Data Pendukung Diri adalah data pribadi dari administrator sebagai pengguna dari aplikasi. Data ini yang kemudian akan ditampilkan ke halaman utama aplikasi.

1. Data Pendukung Diri disusun secara key - value di level database
2. Data Pendukung Diri memiliki konfigurasi bawaan,
	- Nama Lengkap
	- Email
	- Kontak
	- Alamat
	- Photo (nullable)
	- Curriculum Vitae (nullable)
	- Deskripsi Diri (nullable)
	- Social Media (facebook, github, linkedIn, twitter, instagram)
3. Data Pendukung Diri memiliki sebuah File Photo
4. Data Pendukung Diri memiliki sebuah File Curriculum Vitae

#### Relasi

1. Data Pendukung Diri 0 to one File (photo) ; File (photo) belongs to a Data Pendukung Diri
1. Data Pendukung Diri 0 to one File (cv) ; File (cv) belongs to a Data Pendukung Diri

### Pendidikan

Pendidikan adalah rincian riwayat pendidikan dari developer.

1. Pendidikan memiliki attribut,
	- Institusi
	- Tingkat
	- Tanggal Mulai (format bulan - tahun)
	- Tahun Tamat (format bulan - tahun)
2. Pendidikan bisa tidak memiliki tahun tamat (alias sedang berlangsung)
3. Pendidikan dengan status sedang berlangsung bisa diperbaharui suatu saat

### Pengalaman

Pengalaman merupakan rincian pengalaman kerja yang dimiliki. 

1. Pengalaman memiliki attribut,
	- Nama Perusahaan
	- Posisi
	- Detail
	- Periode Mulai
	- Periode Selesai
2. Pengalaman bisa tidak memiliki tahun selesai (alias sedang berlangsung)
3. Pengalaman dengan status sedang berlangsung bisa diperbaharui suatu saat

### Skill set

Skill set merupakan list skill yang dikuasai pengguna.

1. Sebuah Skill memiliki banyak Sertifikat
2. Skill memiliki attribut,
	- Nama
	- Deskripsi
	- Level (Beginner/Medium/Expert)
	- Attachment

#### Relasi

1. Skill has 0 to many Sertifikat; Sertifikat belongs to a Skill

### Serfitikat

Sertifikat merupakan daftar sertifikat yang dimiliki oleh pengguna.

1. Satu Sertifikat milik satu Skill
2. Sertifikat memiliki attribut,
	- Judul
	- Bidang / Topik
	- Deskripsi
	- Vendor / Penerbit Sertifikat
	- Periude Mulai 
	- Periode Selesai (kadaluarsa)
	- Attachment
3. Sertifikat bisa memiliki periode yang tidak disebutkan (alias permanen) 
4. Sertifikat memiliki File Attachment sebagai bukti lembaran sertifikat

#### Relasi

1. Sertifikat belongs to a Skill ; Skill has 0 to many Sertifikat
2. Sertifikat has 0 to many File ; File belongs to a Sertifikat 

### Portofolio

Portofolio merupakan fitur yang digunakan untuk mengatur daftar portofolio yang dimiliki.

1. Portofolio memiliki attribut,
	- Judul
	- Deskripsi
	- Url Live Preview (nullable)
	- Url Repository (nullable)
	- Tanggal dikerjakan (nullable)
	- Durasi Pengerjaan (nullable)
	- Teknologi (string dipisahka dengan koma, misal php, laravel, js)
	- Thumbnail Location
	- Preview
2. Portofolio bisa memiliki banyak File (Photo) Preview

#### Relasi
	
1. Portofolio has 0 to many Files / Photo Preview ; File belongs to a Portofolio

### File

File merupakan table pada database yang digunakna untuk mencatat seluruh file yang berada pada sistem

1. File memiliki attribute
	- Name
	- Tipe (class relasi)
	- Identifier (nullable) [digunakan untuk memberikan keterangan bagi file dari satu class relasi yang sama tetapi dengan dua tipe file yang berbeda]
	- Lokasi File / URL

#### Relasi

1. File belongs to a Portofolio (indentifier=preview) ; Portofolio has 0 to many File / Photo Preview
2. File belongs to a Sertifikat (indentifier=certificate) ; Sertifikat has 0 to many File
3. File belongs to a Data Pendukung Diri (indentifier=photo) ; Data Pendukung Diri has 0 to one File
4. File belongs to a Data Pendukung Diri (indentifier=curriculum_vitae) ; Data Pendukung Diri has 0 to one File
5. File belongs to a Perpesanan (identifier=attachment) ; Perpesanan has 0 to many File 

### Perpesanan (Hiring / Tawaran)

Perpesanaan ini digunakan untuk merekap pesan yang dikirimkan melalui form 'hire me' atau kirim pesan.

1. Perpesanan memiliki attribut,
	- Nama Pengirim
	- Email Pengirim
	- Tujuan (Judul)
	- Isi Pesan
	- Attachement
2. Perpesanan bisa memiliki file attachment

#### Relasi

1. Perpesanan has 0 to many File ; File belongs to a Perpesanan

### Pelaporan

#### Laporan Kunjugan Website

- Laporan menggunakan google analitycs. (opsi)
- Laporan digunakan untuk merekap berapa kunjugan website secara periodik, yaitu harian, mingguan dan bulanan. (opsi)

#### Laporan Jumlah Unduh Curriculum Vitae (masih rencana)

1. Laporan ini bekerja dengan menghitung berapa kali klik yang dilakukan terhadap tombol see / download cv.
2. Laporan ini memiliki attribute : 
	- IP Address atau Lokasi
	- Tanggal Download (created_at)

#### Laporan Email / Pesan

## User Interface 

### Visual Installer (saat pertama kali menjalankan aplikasi)

### Front Page

### Administrator Page
