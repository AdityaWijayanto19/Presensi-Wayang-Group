#!/bin/bash
# ============================================================
# setup-cron.sh - Setup Laravel Scheduler Cron Job
# Untuk Hostinger Shared Hosting / VPS
# ============================================================
# Cara pakai:
#   chmod +x setup-cron.sh
#   ./setup-cron.sh
# ============================================================

set -e

# ============================================================
# CONFIG
# ============================================================
PHP_BIN="/opt/alt/php84/usr/bin/php"
APP_DIR="$(pwd)"

# ============================================================
# COLORS
# ============================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info()    { echo -e "${CYAN}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; }

# ============================================================
# HEADER
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     CRON JOB SETUP - Laravel Scheduler       ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""

# ============================================================
# 0. CHECK PHP
# ============================================================
info "Checking PHP binary..."
if [ ! -f "$PHP_BIN" ]; then
    error "PHP binary not found at $PHP_BIN"
    echo "Edit variable PHP_BIN di script ini sesuai path PHP server kamu."
    exit 1
fi
success "PHP found"

# ============================================================
# 1. CHECK CURRENT CRON
# ============================================================
info "Checking existing cron jobs..."
CURRENT_CRON=$(crontab -l 2>/dev/null || echo "")

if echo "$CURRENT_CRON" | grep -qF "artisan schedule:run"; then
    warn "Cron job Laravel scheduler SUDAH ada!"
    echo ""
    echo "Cron entry yang ada:"
    echo "$CURRENT_CRON" | grep "artisan schedule:run"
    echo ""
    read -p "Tetap update/replace? (y/n): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        info "Dibatalkan. Cron job existing dipertahankan."
        exit 0
    fi

    # Hapus entry lama
    info "Removing old cron entry..."
    echo "$CURRENT_CRON" | grep -v "artisan schedule:run" | crontab -
    success "Old entry removed"
fi

# ============================================================
# 2. ADD NEW CRON JOB
# ============================================================
info "Adding Laravel scheduler cron job..."
info "Schedule: Setiap 1 menit (cron default Laravel)"
info "Tasks:"
echo "  - wfh:mark-unpaid      (jam 00:00)"
echo "  - wfh:reminder-laporan  (jam 22:00)"
echo ""

CRON_LINE="* * * * * cd ${APP_DIR} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1"

(crontab -l 2>/dev/null; echo "$CRON_LINE") | crontab -

success "Cron job berhasil ditambahkan!"

# ============================================================
# 3. VERIFY
# ============================================================
info "Verifying cron entry..."
NEW_CRON=$(crontab -l 2>/dev/null)

if echo "$NEW_CRON" | grep -qF "artisan schedule:run"; then
    echo ""
    success "Cron job verified!"
    echo ""
    echo "Isi crontab kamu:"
    echo "---"
    echo "$NEW_CRON" | grep -v "^#$" | grep -v "^$"
    echo "---"
else
    error "Gagal menambahkan cron job!"
    echo "Coba tambahkan manual:"
    echo ""
    echo "  crontab -e"
    echo "  Tambahkan baris ini:"
    echo "  $CRON_LINE"
    exit 1
fi

# ============================================================
# 4. TEST SCHEDULE
# ============================================================
echo ""
info "Testing scheduler..."
if $PHP_BIN artisan schedule:list 2>/dev/null; then
    echo ""
    success "Scheduler tasks:"
    echo "  [x] wfh:mark-unpaid      → setiap hari jam 00:00"
    echo "  [x] wfh:reminder-laporan  → setiap hari jam 22:00"
else
    warn "Tidak bisa test schedule:list (mungkin belum migrate)"
fi

# ============================================================
# DONE
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        CRON JOB SETUP COMPLETE!               ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo "Cron job yang aktif:"
echo "  * * * * * → cd $APP_DIR && $PHP_BIN artisan schedule:run"
echo ""
echo "Scheduled tasks:"
echo "  - 00:00  → wfh:mark-unpaid (tandai WFH belum upload laporan)"
echo "  - 22:00  → wfh:reminder-laporan (reminder upload laporan)"
echo ""
echo "Untuk cek manual:"
echo "  crontab -l"
echo ""
echo "Untuk test scheduler:"
echo "  $PHP_BIN artisan schedule:run"
echo ""
