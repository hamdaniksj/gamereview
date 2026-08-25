Step

1. Clone Repository dari GitHub
2. Konfigurasi .env
3. Install Dependencies menggunakan Composer "composer install --no-dev"
4. Generate App Key
5. Berikan Hak Akses (Permissions) Folder Storage
6. Jalankan Migrasi Database

Jika dengan docker jalankan
docker build -t games-review-app .
docker run -d -p 8080:80 --name games-review-container games-review-app
