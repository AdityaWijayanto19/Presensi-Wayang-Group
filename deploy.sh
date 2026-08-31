#!/bin/bash
# ============================================================
# deploy.sh - Presensi Digital Production Deployment
# Hostinger Shared Hosting (CloudLinux)
# ============================================================
# Cara pakai:
#   chmod +x deploy.sh
#   ./deploy.sh
#
# CATATAN:
#   - .env harus dibuat/diisi MANUAL sebelum jalankan script ini
#   - Frontend assets (public/build) harus di-upload manual dari local
#     (Node.js/npm tidak tersedia di shared hosting)
# ============================================================

set -e

# ============================================================
# CONFIG
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
# HELPER
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
    echo "Coba cari: ls /opt/alt/php*/usr/bin/php"
    exit 1
fi

# ============================================================
# 1. CHECK .ENV FILE
# ============================================================
if [ ! -f .env ]; then
    error ".env file tidak ditemukan!"
    echo ""
    echo "Buat .env manual:"
    echo "  cp .env.example .env"
    echo "  nano .env"
    echo ""
    echo "Isi minimal:"
    echo "  APP_ENV=production"
    echo "  APP_DEBUG=false"
    echo "  APP_URL=https://test-presensi.wayang.group"
    echo "  APP_KEY=base64:..."
    echo "  DB_HOST=localhost"
    echo "  DB_DATABASE=..."
    echo "  DB_USERNAME=..."
    echo "  DB_PASSWORD=..."
    echo ""
    echo "Setelah edit .env, jalankan lagi: ./deploy.sh"
    exit 1
fi

# Cek APP_KEY
info "Checking APP_KEY..."
KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
if [ -z "$KEY_VALUE" ]; then
    warn "APP_KEY kosong! Generate manual:"
    echo "  /opt/alt/php84/usr/bin/php artisan key:generate --force"
    echo "  Atau: sed -i 's/^APP_KEY=.*/APP_KEY=base64:\$(openssl rand -base64 32)/' .env"
    exit 1
else
    success "APP_KEY sudah ada"
fi

# ============================================================
# 2. COMPOSER INSTALL (Production)
# ============================================================
info "Installing composer dependencies..."
if command -v composer &> /dev/null; then
    $PHP_BIN $(which composer) install --no-dev --optimize-autoloader --no-interaction
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
# 3. ROOT .HTACCESS (Rewrite ke public/)
# ============================================================
if [ ! -f .htaccess ]; then
    info "Membuat root .htaccess (rewrite ke public/)..."
    cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
    success "Root .htaccess dibuat"
else
    # Cek apakah sudah rewrite ke public/
    if grep -q "RewriteRule.*public/" .htaccess; then
        success "Root .htaccess sudah benar (rewrite ke public/)"
    else
        warn "Root .htaccess bukan rewrite ke public/!"
        echo "Isi .htaccess sekarang:"
        cat .htaccess
        echo ""
        read -p "Ganti dengan rewrite ke public/? (y/n): " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            cp .htaccess .htaccess.bak
            cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
            success "Root .htaccess diganti"
        fi
    fi
fi

# ============================================================
# 4. HAPUS ROOT INDEX.PHP (jika ada)
# ============================================================
if [ -f index.php ]; then
    warn "Root index.php ditemukan! Ini tidak diperlukan (sudah ada di public/)."
    read -p "Hapus index.php dari root? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm index.php
        success "Root index.php dihapus"
    fi
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
echo "  [x] Root .htaccess (rewrite ke public/)"
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
echo "   Command:"
echo "   cd $APP_DIR && $PHP_BIN artisan schedule:run >> /dev/null 2>&1"
echo "   Schedule: Semua field isi * (setiap menit)"
echo ""
echo "2. UPLOAD FRONTEND ASSETS (jika ada perubahan CSS/JS):"
echo "   Jalankan dari local (Laragon):"
echo "   .\\deploy-assets.ps1"
echo ""
echo "3. CLEAR PUSH SUBSCRIPTIONS (karena VAPID keys baru):"
echo "   $PHP_BIN artisan tinker"
echo "   >>> DB::table('push_subscriptions')->truncate();"
echo ""
echo "4. TEST:"
echo "   curl -I https://test-presensi.wayang.group"
echo "   Buka di browser: https://test-presensi.wayang.group"
echo ""
