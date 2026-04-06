@echo off
echo ============================================
echo   TuCancha - Setup Project
echo ============================================
echo.

:: Verificar dependencias
echo Verificando dependencias...
where php >nul 2>nul || (echo ERROR: PHP no encontrado. Ejecuta setup-dev-pc.bat primero. && pause && exit /b 1)
where composer >nul 2>nul || (echo ERROR: Composer no encontrado. Ejecuta setup-dev-pc.bat primero. && pause && exit /b 1)
where node >nul 2>nul || (echo ERROR: Node.js no encontrado. Ejecuta setup-dev-pc.bat primero. && pause && exit /b 1)
where git >nul 2>nul || (echo ERROR: Git no encontrado. Ejecuta setup-dev-pc.bat primero. && pause && exit /b 1)

echo PHP:      && php -v | findstr /i "PHP"
echo Node:     && node -v
echo Composer: && composer -V | findstr /i "Composer"
echo Git:      && git --version
echo.

:: Habilitar extensiones PHP necesarias
echo ============================================
echo   Extensiones PHP requeridas
echo ============================================
echo Asegurate de que estas extensiones esten habilitadas en php.ini:
echo   extension=curl
echo   extension=fileinfo
echo   extension=gd
echo   extension=mbstring
echo   extension=openssl
echo   extension=pdo_mysql
echo   extension=zip
echo.

:: Instalar dependencias
echo ============================================
echo   Instalando dependencias PHP...
echo ============================================
call composer install
if %errorlevel% neq 0 (
    echo ERROR: composer install fallo. Revisa los errores arriba.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Instalando dependencias JS...
echo ============================================
call npm install
if %errorlevel% neq 0 (
    echo ERROR: npm install fallo. Revisa los errores arriba.
    pause
    exit /b 1
)

:: Configurar .env
echo.
echo ============================================
echo   Configurando .env...
echo ============================================
if not exist .env (
    copy .env.example .env
    echo .env creado desde .env.example
    echo.
    echo IMPORTANTE: Edita .env con tus datos de MySQL:
    echo   DB_DATABASE=tucancha
    echo   DB_USERNAME=root
    echo   DB_PASSWORD=tu_password
    echo.
) else (
    echo .env ya existe, no se sobreescribe.
)

:: Generar key
echo.
echo ============================================
echo   Generando application key...
echo ============================================
php artisan key:generate

:: Crear base de datos
echo.
echo ============================================
echo   Base de datos
echo ============================================
echo Asegurate de tener MySQL corriendo y haber creado la base de datos.
echo Podes crearla con: mysql -u root -p -e "CREATE DATABASE tucancha;"
echo.
set /p DB_READY=Ya tenes la DB creada y .env configurado? (s/n):
if /i "%DB_READY%"=="s" (
    echo Corriendo migraciones...
    php artisan migrate --seed
    if %errorlevel% neq 0 (
        echo ERROR: Las migraciones fallaron. Revisa la conexion a MySQL en .env
        pause
        exit /b 1
    )
    echo Migraciones completadas!
) else (
    echo OK, cuando tengas la DB lista ejecuta: php artisan migrate --seed
)

echo.
echo ============================================
echo   Setup completado!
echo ============================================
echo.
echo Para levantar el servidor:
echo   Terminal 1: composer dev
echo   Terminal 2: npm run dev
echo.
echo La app va a estar en: http://localhost:8000
echo ============================================
pause
