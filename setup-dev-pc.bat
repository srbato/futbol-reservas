@echo off
echo ============================================
echo   TuCancha - Setup Dev Environment
echo ============================================
echo.

:: Verificar si winget esta disponible
where winget >nul 2>nul
if %errorlevel% neq 0 (
    echo ERROR: winget no esta disponible. Instalalo desde Microsoft Store (App Installer).
    pause
    exit /b 1
)

echo [1/6] Instalando PHP 8.4...
winget install --id PHP.PHP.8.4 --accept-package-agreements --accept-source-agreements

echo.
echo [2/6] Instalando Composer...
winget install --id Composer.Composer --accept-package-agreements --accept-source-agreements

echo.
echo [3/6] Instalando Node.js LTS...
winget install --id OpenJS.NodeJS.LTS --accept-package-agreements --accept-source-agreements

echo.
echo [4/6] Instalando MySQL Server...
winget install --id Oracle.MySQL --accept-package-agreements --accept-source-agreements

echo.
echo [5/6] Instalando Git...
winget install --id Git.Git --accept-package-agreements --accept-source-agreements

echo.
echo [6/6] Instalando Redis (Memurai)...
winget install --id Memurai.Memurai.Developer --accept-package-agreements --accept-source-agreements

echo.
echo ============================================
echo   Instalacion completada!
echo ============================================
echo.
echo IMPORTANTE: Cerra y volve a abrir la terminal
echo para que se actualicen los PATH.
echo.
echo Despues ejecuta setup-project.bat para
echo configurar el proyecto TuCancha.
echo ============================================
pause
