#!/bin/bash
# ============================================================
# setup-cron.sh - Setup Laravel Scheduler
# Hostinger Shared Hosting (CloudLinux)
# ============================================================
# Cron job harus dibuat lewat hPanel (SSH crontab dibatasi)
# ============================================================

PHP_BIN="/opt/alt/php84/usr/bin/php"
APP_DIR="$(pwd)"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     CRON JOB SETUP - Laravel Scheduler       ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${YELLOW}============================================${NC}"
echo -e "${YELLOW}  SETUP MANUAL VIA hPanel                  ${NC}"
echo -e "${YELLOW}============================================${NC}"
echo ""
echo "1. Login ke hPanel"
echo "2. Klik Manage di VPS kamu"
echo "3. Cari menu Cron Jobs"
echo "4. Klik Create New / Tambah Cron Job"
echo ""
echo -e "${CYAN}Isi form seperti ini:${NC}"
echo ""
echo "  Perintah untuk Dijalankan:"
echo "  cd ${APP_DIR} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "  Pilihan Umum:"
echo "    Menit     : *"
echo "    Jam       : *"
echo "    Hari      : *"
echo "    Bulan     : *"
echo "    Hari Kerja: *"
echo ""
echo "5. Klik Simpan"
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  SCHEDULED TASKS                           ${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo "Laravel Scheduler akan menjalankan:"
echo "  - 00:00  wfh:mark-unpaid"
echo "  - 22:00  wfh:reminder-laporan"
echo ""
echo "Scheduler otomatis jalan setiap menit,"
echo "lalu dia yang tentuin task mana yang dijalankan berdasarkan jam."
echo ""
