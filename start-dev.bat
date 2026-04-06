@echo off
echo ============================================
echo   TuCancha - Iniciando entorno de desarrollo
echo ============================================
echo.

cd /d "%~dp0"

echo Abriendo servidores...
echo.

start "Laravel (8000)"  cmd /k "php artisan serve"
start "Vite (5173)"     cmd /k "npm run dev"
start "Queue Worker"    cmd /k "php artisan queue:work"
start "Reverb (8080)"   cmd /k "php artisan reverb:start"
start "Scheduler"       cmd /k "php artisan schedule:work"

echo Listo! Se abrieron 5 terminales:
echo   - Laravel:   http://localhost:8000
echo   - Vite:      http://localhost:5173
echo   - Queue, Reverb y Scheduler corriendo en background
echo.
echo Podes cerrar esta ventana.
pause
