#!/bin/bash
# ============================================================
# setup-cron.sh - Setup Laravel Scheduler (Manual Instructions)
# Untuk Hostinger VPS (crontab tidak tersedia di container)
# ============================================================
# Cron job harus di-setup manual lewat hPanel:
#   hPanel > VPS > Cron Jobs > Create New
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

# Cek crontab
if command -v crontab &> /dev/null; then
    echo "Crontab tersedia! Setting up..."

    CRON_LINE="* * * * * cd ${APP_DIR} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1"

    CURRENT_CRON=$(crontab -l 2>/dev/null || echo "")

    if echo "$CURRENT_CRON" | grep -qF "artisan schedule:run"; then
        echo "Cron job Laravel scheduler sudah ada!"
        echo ""
        echo "Entry yang ada:"
        echo "$CURRENT_CRON" | grep "artisan schedule:run"
    else
        (crontab -l 2>/dev/null; echo "$CRON_LINE") | crontab -
        echo "Cron job berhasil ditambahkan!"
    fi
else
    echo -e "${YELLOW}Crontab tidak tersedia di VPS ini (CloudLinux container).${NC}"
    echo ""
    echo "Setup manual lewat hPanel:"
    echo ""
fi

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  INSTRUKSI SETUP CRON VIA hPanel           ${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo "1. Login ke hPanel"
echo "2. Klik Manage di VPS kamu"
echo "3. Cari menu Cron Jobs"
echo "4. Klik Create New / Tambah Cron Job"
echo ""
echo "Isi form seperti ini:"
echo ""
echo -e "${CYAN}Perintah untuk Dijalankan:${NC}"
echo "  cd ${APP_DIR} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1"
echo ""
echo -e "${CYAN}Pilihan Umum:${NC}"
echo "  Menit     : * (setiap menit)"
echo "  Jam       : * (setiap jam)"
echo "  Hari      : * (setiap hari)"
echo "  Bulan     : * (setiap bulan)"
echo "  Hari Kerja: * (semua hari)"
echo ""
echo "5. Klik Simpan"
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  SCHEDULED TASKS                           ${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo "Laravel Scheduler akan menjalankan:"
echo "  - 00:00  wfh:mark-unpaid      (tandai WFH belum upload laporan)"
echo "  - 22:00  wfh:reminder-laporan  (reminder upload laporan)"
echo ""
echo "Scheduler otomatis jalan setiap menit,"
echo "lalu dia yang tentuin task mana yang dijalankan berdasarkan jam."
echo ""
