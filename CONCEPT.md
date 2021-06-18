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
	- Photo
	- Curriculum Vitae
	- Deskripsi Diri
	- Social Media (facebook, github, linkedIn, twitter, instagram)

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

#### Relasi

1. Skill has 0 to many Sertifikat

### Serfitikat

Sertifikat merupakan daftar sertifikat yang dimiliki oleh pengguna.

1. Satu Sertifikat milik satu Skill
2. Sertifikat memiliki attribut,
	- Judul
	- Bidang / Topik
	- Deskripsi
	- Periude Mulai 
	- Periode Selesai (kadaluarsa)
3. Sertifikat bisa memiliki periode yang tidak disebutkan (alias permanen) 

#### Relasi

1. Sertifikat belongs to a Skill - Skill has 0 to many Sertifikat



### User Interface (front side)