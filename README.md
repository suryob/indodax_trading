1. Prasyarat
	- php versi 5.6 atau yang lebih baru
	- mysql server database
	- indodax api private key & secret key
2. penggunaan
	- Langkah installasi
		- Login ke mysql server
		- Buat user mysql
		- Buat database dengan nama koinkoin
		- Import file intial.sql yang berada pada folder sql/
		- Isikan nama user dan password mysql yang tadi dibuat pada file config.php yang berada pada folder lib/
		- Isikan private key & secret key indodax pada file cred.php yang berada pada folder lib/
	- Menjalankan aplikasi trading
		- Jalankan file start.sh yang berada pada folder cron/
	- Menjalankan web
		- Jalankan file start.sh yang berada pada folder cron/
		Web dapat diakses dengan menggunakan browser dengan mengetikan url http://localhost:9999
3. Fitur
	- Pengecekan perubahan harga untuk menentukan waktu pembelian
	- Batas bawah selisih harga ketika beli dan harga berjalan
	Dapat diubah pada file config.php yang berada pada folder lib/
	- Batas atas selisih harga ketika beli dan harga berjalan
	Dapat diubah pada file config.php yang berada pada folder lib/
	- Maksimal penjualan rugi per koin per hari
	Dapat diubah pada file config.php yang berada pada folder lib/
	- Memilih koin yang ingin ditransaksikan pada web
	- Menambahkan koin yang ingin ditransaksikan pada web
	- Melakukan setting harga beli maksimum per koin pada web

------------


### Saya tidak bertanggung jawab atas kerugian yang terjadi, resiko yang terjadi akibat trading anda tanggung sendiri
