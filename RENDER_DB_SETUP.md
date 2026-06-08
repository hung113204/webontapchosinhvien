# Dua database MySQL len Render

File nay dung de lam checklist khi dua database local len Render. Khong ghi mat khau that vao file nay neu file co the bi commit len Git.

## 1. Thong tin DB local hien tai

Lay tu file `.env` local:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=decuongonthi
DB_USERNAME=root
DB_PASSWORD=
```

Database can export: `decuongonthi`

## 2. Tao MySQL Private Service tren Render

Render khong dung `127.0.0.1` cua may local. Can tao mot MySQL service rieng tren Render.

Tao service:

- Type: Private Service
- Runtime: Docker
- Database: MySQL 8
- Disk mount path: `/var/lib/mysql`
- Disk size: toi thieu `10 GB`

Env cho MySQL service tren Render:

```env
MYSQL_DATABASE=decuongonthi
MYSQL_USER=decuongonthi_user
MYSQL_PASSWORD=DIEN_MAT_KHAU_MANH
MYSQL_ROOT_PASSWORD=DIEN_ROOT_PASSWORD_MANH
```

Sau khi MySQL service deploy xong, Render se hien host noi bo dang tuong tu:

```text
mysql-xxxx:3306
```

Ghi lai phan host, vi du:

```text
DB_HOST=mysql-xxxx
DB_PORT=3306
```

## 3. Env can dien cho Laravel Web Service tren Render

Vao Laravel web service tren Render, them/cap nhat Environment Variables:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:DIEN_APP_KEY_CUA_BAN
APP_DEBUG=false
APP_URL=https://WebontP.onrender.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql-xxxx
DB_PORT=3306
DB_DATABASE=decuongonthi
DB_USERNAME=decuongonthi_user
DB_PASSWORD=DIEN_MAT_KHAU_MYSQL_RENDER

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

Tao `APP_KEY` bang lenh local:

```bash
php artisan key:generate --show
```

Neu dung Google Login, sua redirect URL thanh domain Render:

```env
GOOGLE_REDIRECT_URL=https://TEN_APP_CUA_BAN.onrender.com/auth/google/callback
```

Dong thoi vao Google Cloud Console them Authorized redirect URI giong URL tren.

## 4. Export database local

Cach 1: Dung terminal voi XAMPP MySQL.

```bash
C:\xampp\mysql\bin\mysqldump.exe -u root decuongonthi > decuongonthi.sql
```

Neu MySQL root local co password:

```bash
C:\xampp\mysql\bin\mysqldump.exe -u root -p decuongonthi > decuongonthi.sql
```

Cach 2: Dung phpMyAdmin.

- Vao phpMyAdmin
- Chon database `decuongonthi`
- Chon Export
- Format: SQL
- Tai ve file `decuongonthi.sql`

## 5. Import database vao MySQL Render

Vi MySQL Render la Private Service, may local thuong khong ket noi truc tiep duoc vao DB. Cach de thao tac de nhat la deploy tam Adminer trong cung Render workspace.

Dang nhap Adminer:

```text
System: MySQL
Server: mysql-xxxx
Username: decuongonthi_user
Password: DIEN_MAT_KHAU_MYSQL_RENDER
Database: decuongonthi
```

Sau do import file:

```text
decuongonthi.sql
```

Neu file SQL qua lon, nen gzip file:

```bash
gzip decuongonthi.sql
```

Va import file:

```text
decuongonthi.sql.gz
```

## 6. Chay lenh sau khi import

Vao Shell cua Laravel web service tren Render, chay:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
```

Neu DB dump da co day du bang, `migrate --force` chi dung de chay cac migration con thieu.

## 7. Kiem tra nhanh

Mo cac URL:

```text
https://TEN_APP_CUA_BAN.onrender.com/healthz
https://TEN_APP_CUA_BAN.onrender.com/
```

`/healthz` phai tra ve:

```text
ok
```

Neu trang chu loi database, kiem tra lai:

- `DB_HOST` co dung host noi bo cua MySQL Render khong
- `DB_DATABASE` co dung `decuongonthi` khong
- `DB_USERNAME` va `DB_PASSWORD` co trung voi MySQL service khong
- MySQL service va Laravel web service co nam cung Render workspace/region khong
- Da import file `decuongonthi.sql` chua
