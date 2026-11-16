# =====================================================================
# SUKARIA — Setup Database Sekali Klik
# Jalankan:  powershell -ExecutionPolicy Bypass -File setup-database.ps1
# (atau klik kanan file ini > Run with PowerShell)
# Anda akan diminta password root MySQL sekali.
# =====================================================================

$ErrorActionPreference = "Stop"

$mysqlCandidates = @(
    "I:\ServBay\packages\mariadb\11.4\bin\mysql.exe",
    "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe",
    "C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe"
)
$dbPort = 3307   # MariaDB 11.4 ServBay dijalankan di port 3307 (port 3306 dipakai MySQL80 instalasi lain)

$mysql = $mysqlCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1
if (-not $mysql) {
    Write-Host "ERROR: mysql.exe tidak ditemukan. Sesuaikan path di script ini." -ForegroundColor Red
    exit 1
}

$sqlFile = Join-Path $PSScriptRoot "database\bootstrap.sql"
if (-not (Test-Path $sqlFile)) {
    Write-Host "ERROR: bootstrap.sql tidak ditemukan di folder database." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "=== SUKARIA Setup Database ===" -ForegroundColor Yellow
Write-Host "MySQL : $mysql"
Write-Host "SQL   : $sqlFile"
Write-Host ""
$pwd = Read-Host "Masukkan password root MySQL (kosongkan bila tanpa password)"

Write-Host ""
Write-Host "Menjalankan bootstrap.sql ..." -ForegroundColor Cyan
if ($pwd -eq "") {
    & $mysql -h 127.0.0.1 -P $dbPort -u root --default-character-set=utf8mb4 -e "source $sqlFile"
} else {
    & $mysql -h 127.0.0.1 -P $dbPort -u root "-p$pwd" --default-character-set=utf8mb4 -e "source $sqlFile"
}

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "SUKSES! Database sukaria_db + user sukaria_app + data dummy siap." -ForegroundColor Green
    Write-Host "Buka aplikasi: http://localhost:8010  (dev server)" -ForegroundColor Green
    Write-Host "Login admin  : admin@sukaria.id / admin123"
    Write-Host "Login seller : seller@sukaria.id / seller123"
} else {
    Write-Host ""
    Write-Host "GAGAL. Periksa password root Anda dan coba lagi." -ForegroundColor Red
}
