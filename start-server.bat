@echo off
echo ============================================
echo  Khoi dong BTL-LTWeb (cong 8001)
echo ============================================

:: Tat cac tien trinh cu neu con chay
echo [1] Tat PHP cu...
taskkill /F /IM php.exe >nul 2>&1

:: Kiem tra MySQL da chay chua
echo [2] Kiem tra MySQL...
netstat -ano | findstr ":3306" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo     MySQL da chay.
) else (
    echo     Khoi dong MySQL...
    start "" /B "C:\xampp_new\mysql\bin\mysqld.exe" --defaults-file="C:\xampp_new\mysql\bin\my.ini" --standalone
    timeout /t 4 /nobreak >nul
    echo     MySQL da bat dau.
)

:: Khoi dong Laravel server
echo [3] Khoi dong Laravel server tren cong 8001...
start "" /B "C:\xampp_new\php\php.exe" -S 127.0.0.1:8001 -t public "C:\xampp_new\htdocs\BTL-LTWeb-main (1)\BTL-LTWeb-main\server.php"
timeout /t 2 /nobreak >nul

echo.
echo ============================================
echo  San sang! Mo trinh duyet tai:
echo  http://127.0.0.1:8001
echo ============================================
echo.
echo Nhan phim bat ky de thoat (server van chay ngam)...
pause >nul
