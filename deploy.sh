#!/bin/bash
# ============================================================
# deploy.sh - Presensi Digital Production Deployment Script
# Untuk Hostinger VPS / Shared Hosting
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
NC='\033[0m'

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
info "Project directory: $APP_DIR"
echo ""

# ============================================================
# 0. CHECK PHP BINARY
# ============================================================
info "Checking PHP binary..."
if [ -f "$PHP_BIN" ]; then
    PHP_VERSION=$($PHP_BIN -r 'echo PHP_VERSION;')
    success "PHP $PHP_VERSION found"
else
    error "PHP binary not found at $PHP_BIN"
    echo "Coba cari PHP manual:"
    echo "  which php"
    echo "  ls /opt/alt/php*/usr/bin/php"
    echo ""
    echo "Edit variable PHP_BIN di deploy.sh sesuai path PHP kamu."
    exit 1
fi

# ============================================================
# 1. CHECK .ENV FILE
# ============================================================
if [ ! -f .env ]; then
    warn ".env file tidak ditemukan!"
    info "Membuat .env dari .env.example..."
    cp .env.example .env
    success ".env dibuat dari .env.example"

    info "Generating APP_KEY..."
    $PHP_BIN artisan key:generate --ansi
    success "APP_KEY generated"

    echo ""
    echo -e "${YELLOW}============================================${NC}"
    echo -e "${YELLOW}  EDIT .env TERLEBIH DAHULU!                ${NC}"
    echo -e "${YELLOW}============================================${NC}"
    echo ""
    echo "Jalankan: nano .env"
    echo ""
    echo "Yang harus diisi:"
    echo "  APP_ENV        = production"
    echo "  APP_DEBUG      = false"
    echo "  APP_URL        = https://test-presensi.wayang.group"
    echo "  DB_HOST        = localhost"
    echo "  DB_DATABASE    = nama_database dari hPanel"
    echo "  DB_USERNAME    = username_database dari hPanel"
    echo "  DB_PASSWORD    = password_database dari hPanel"
    echo ""
    echo "Setelah edit, jalankan lagi: ./deploy.sh"
    exit 1
fi

# Cek APP_KEY
info "Checking APP_KEY..."
KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
if [ -z "$KEY_VALUE" ]; then
    info "APP_KEY kosong, generating..."
    $PHP_BIN artisan key:generate --ansi
    success "APP_KEY generated"
else
    success "APP_KEY sudah ada"
fi

# Cek APP_ENV
APP_ENV=$(grep "^APP_ENV=" .env | cut -d'=' -f2)
if [ "$APP_ENV" = "local" ]; then
    warn "APP_ENV masih 'local'! Seharusnya 'production'"
    warn "Edit .env: APP_ENV=production"
fi

# Cek APP_DEBUG
APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d'=' -f2)
if [ "$APP_DEBUG" = "true" ]; then
    warn "APP_DEBUG masih 'true'! Seharusnya 'false' untuk production"
    warn "Edit .env: APP_DEBUG=false"
fi

# ============================================================
# 2. COMPOSER INSTALL (Production)
# ============================================================
info "Installing composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
    success "Composer install selesai"
elif [ -f "$APP_DIR/composer.phar" ]; then
    $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction
    success "Composer install selesai (via composer.phar)"
else
    warn "Composer tidak ditemukan, download composer.phar..."
    curl -sS https://getcomposer.org/installer | $PHP_BIN -- --quiet
    $PHP_BIN composer.phar install --no-dev --optimize-autoloader --no-interaction
    success "Composer install selesai (via downloaded composer.phar)"
fi

# ============================================================
# 3. NPM INSTALL & BUILD ASSETS (Vite)
# ============================================================
if [ -f package.json ]; then
    info "Installing NPM dependencies..."
    npm install --production=false 2>/dev/null
    success "NPM install selesai"

    info "Building Vite assets..."
    npm run build
    success "Assets built ke public/build/"

    # Hapus hot file (Vite dev server flag)
    if [ -f public/hot ]; then
        rm -f public/hot
        success "File public/hot dihapus"
    fi
else
    warn "package.json tidak ditemukan, skip npm build"
fi

# ============================================================
# 4. RUN MIGRATIONS
# ============================================================
echo ""
read -p "Jalankan migration sekarang? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    info "Running database migrations..."
    $PHP_BIN artisan migrate --force
    success "Migrations selesai"

    read -p "Jalankan seeder juga? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        info "Running seeders..."
        $PHP_BIN artisan db:seed --force
        success "Seeders selesai"
    fi
else
    warn "Migration dilewati! Jalankan manual: php artisan migrate --force"
fi

# ============================================================
# 5. STORAGE SYMLINK
# ============================================================
info "Creating storage symlink..."
$PHP_BIN artisan storage:link --force
success "Storage symlink created"

# ============================================================
# 6. SET FOLDER PERMISSIONS
# ============================================================
info "Setting folder permissions..."
chmod -R 775 storage/ 2>/dev/null || true
chmod -R 775 bootstrap/cache/ 2>/dev/null || true
chmod -R 775 public/build/ 2>/dev/null || true
success "Folder permissions: 775"

# ============================================================
# 7. CACHE & OPTIMIZATION
# ============================================================
info "Clearing old caches..."
$PHP_BIN artisan cache:clear 2>/dev/null || true
$PHP_BIN artisan config:clear 2>/dev/null || true
$PHP_BIN artisan route:clear 2>/dev/null || true
$PHP_BIN artisan view:clear 2>/dev/null || true

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
# 8. DONE
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        DEPLOYMENT BERHASIL!                  ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo "Project: $APP_DIR"
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
echo -e "${YELLOW}============================================${NC}"
echo -e "${YELLOW}  LANGKAH SELANJUTNYA (MANUAL):              ${NC}"
echo -e "${YELLOW}============================================${NC}"
echo ""
echo "1. SETUP CRON JOB (via hPanel):"
echo "   Login hPanel > VPS > Cron Jobs > Create New"
echo ""
echo "   Command:"
echo "   cd $APP_DIR && $PHP_BIN artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "   Schedule: Semua field isi * (setiap menit)"
echo ""
echo "2. INSTALL SSL (jika belum):"
echo "   hPanel > SSL > Let's Encrypt > Install"
echo ""
echo "3. TRANSFER USER UPLOADS (jika ada data lama):"
echo "   scp -r local-uploads/ user@server:$APP_DIR/storage/app/public/"
echo ""
echo "4. CLEAR PUSH SUBSCRIPTIONS (karena VAPID keys baru):"
echo "   $PHP_BIN artisan tinker"
echo "   >>> DB::table('push_subscriptions')->truncate();"
echo ""
echo "5. TEST:"
echo "   curl -I https://test-presensi.wayang.group"
echo ""
