#!/bin/bash
# ============================================================
# deploy.sh - Presensi Digital Production Deployment Script
# Untuk Hostinger Shared Hosting / VPS
# ============================================================
# Cara pakai:
#   chmod +x deploy.sh
#   ./deploy.sh
# ============================================================

set -e

# ============================================================
# CONFIG - Sesuaikan jika perlu
# ============================================================
PHP_BIN="/opt/alt/php84/usr/bin/php"

# ============================================================
# COLORS
# ============================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

APP_DIR="$(pwd)"

# ============================================================
# HELPER FUNCTIONS
# ============================================================
info()    { echo -e "${CYAN}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; }

# ============================================================
# HEADER
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     PRESENSI DIGITAL - DEPLOYMENT SCRIPT     ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""

# ============================================================
# 0. CHECK PHP BINARY
# ============================================================
info "Checking PHP binary..."
if [ -f "$PHP_BIN" ]; then
    PHP_VERSION=$($PHP_BIN -r 'echo PHP_VERSION;')
    success "PHP $PHP_VERSION found at $PHP_BIN"
else
    error "PHP binary not found at $PHP_BIN"
    echo "Coba cari PHP manual:"
    echo "  which php"
    echo "  ls /opt/alt/php*/usr/bin/php"
    echo ""
    echo "Set variable PHP_BIN di deploy.sh sesuai path PHP kamu."
    exit 1
fi

# ============================================================
# 1. CHECK .ENV FILE
# ============================================================
if [ ! -f .env ]; then
    warn ".env file tidak ditemukan!"
    info "Membuat .env dari .env.example..."
    cp .env.example .env

    info "Generating APP_KEY..."
    $PHP_BIN artisan key:generate --ansi

    echo ""
    error "STOP! Edit file .env terlebih dahulu!"
    echo ""
    echo "Yang harus diisi:"
    echo "  APP_ENV        = production"
    echo "  APP_DEBUG      = false"
    echo "  APP_URL        = https://domain-kamu.com"
    echo "  DB_HOST        = localhost"
    echo "  DB_DATABASE    = nama_database"
    echo "  DB_USERNAME    = username_database"
    echo "  DB_PASSWORD    = password_database"
    echo ""
    echo "Setelah edit .env, jalankan lagi: ./deploy.sh"
    exit 1
fi

# Cek APP_KEY
if grep -q "APP_KEY=" .env; then
    KEY_VALUE=$(grep "APP_KEY=" .env | cut -d'=' -f2)
    if [ -z "$KEY_VALUE" ] || [ "$KEY_VALUE" = "" ]; then
        info "APP_KEY kosong, generating..."
        $PHP_BIN artisan key:generate --ansi
        success "APP_KEY generated"
    else
        success "APP_KEY sudah ada"
    fi
fi

# ============================================================
# 2. COMPOSER INSTALL (Production)
# ============================================================
info "Installing composer dependencies (--no-dev --optimize-autoloader)..."

# Cek apakah composer tersedia
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
    success "Composer install selesai"
elif [ -f "$APP_DIR/composer.phar" ]; then
    $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction --no-scripts
    success "Composer install selesai (via composer.phar)"
else
    warn "Composer tidak ditemukan! Download composer.phar..."
    curl -sS https://getcomposer.org/installer | $PHP_BIN
    $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction --no-scripts
    success "Composer install selesai (via downloaded composer.phar)"
fi

# ============================================================
# 3. NPM INSTALL & BUILD ASSETS (Vite)
# ============================================================
if [ -f package.json ]; then
    info "Installing NPM dependencies..."
    npm install --production=false
    success "NPM install selesai"

    info "Building Vite assets..."
    npm run build
    success "Assets built ke public/build/"

    # Hapus hot file (Vite dev server flag)
    if [ -f public/hot ]; then
        rm -f public/hot
        success "File public/hot dihapus (dev server flag)"
    fi
else
    warn "package.json tidak ditemukan, skip npm build"
fi

# ============================================================
# 4. RUN MIGRATIONS
# ============================================================
info "Running database migrations..."
read -p "Jalankan migration sekarang? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    $PHP_BIN artisan migrate --force
    success "Migrations selesai"
else
    warn "Migration dilewati! Jalankan manual: php artisan migrate --force"
fi

# ============================================================
# 5. SEEDERS (OPSIONAL)
# ============================================================
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Jalankan seeder juga? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        info "Running seeders..."
        $PHP_BIN artisan db:seed --force
        success "Seeders selesai"
    fi
fi

# ============================================================
# 6. STORAGE SYMLINK
# ============================================================
info "Creating storage symlink..."
$PHP_BIN artisan storage:link --force
success "Storage symlink: public/storage -> storage/app/public"

# ============================================================
# 7. SET FOLDER PERMISSIONS
# ============================================================
info "Setting folder permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Coba set ownership ke www-data (jika punya akses)
if [ "$(id -u)" -eq 0 ]; then
    chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || true
    success "Ownership set ke www-data"
else
    warn "Tidak punya akses root untuk chown. Jika permission error, jalankan:"
    echo "  sudo chown -R www-data:www-data storage/ bootstrap/cache/"
fi

success "Folder permissions: 775"

# ============================================================
# 8. CACHE & OPTIMIZATION
# ============================================================
info "Clearing old caches..."
$PHP_BIN artisan cache:clear
$PHP_BIN artisan config:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear

info "Caching configuration..."
$PHP_BIN artisan config:cache
success "Config cached"

info "Caching routes..."
$PHP_BIN artisan route:cache
success "Routes cached"

info "Caching views..."
$PHP_BIN artisan view:cache
success "Views cached"

info "Running optimize..."
$PHP_BIN artisan optimize
success "Optimize complete"

# ============================================================
# 9. REHASH PASSWORD (opsional, untuk Laravel < 10)
# ============================================================
info "Rehashing passwords..."
$PHP_BIN artisan auth:reminders-table 2>/dev/null || true

# ============================================================
# 10. DONE
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        DEPLOYMENT BERHASIL!                  ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo "Yang sudah dilakukan:"
echo "  [x] Composer install (--no-dev)"
echo "  [x] NPM build assets"
echo "  [x] APP_KEY checked"
echo "  [x] Storage symlink created"
echo "  [x] Folder permissions set (775)"
echo "  [x] Config cached"
echo "  [x] Routes cached"
echo "  [x] Views cached"
echo "  [x] Application optimized"
echo ""
echo -e "${YELLOW}Yang masih harus dilakukan manual:${NC}"
echo ""
echo "  1. Setup Cron Job:"
echo "     ./setup-cron.sh"
echo ""
echo "  2. Transfer user uploads (jika ada data lama):"
echo "     scp -r uploads/ user@server:/path/to/storage/app/public/"
echo ""
echo "  3. Install SSL certificate (jika belum):"
echo "     → hPanel > SSL > Let's Encrypt"
echo ""
echo "  4. Test akses:"
echo "     → https://domain-kamu.com"
echo ""
