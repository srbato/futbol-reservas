# Imagen de desarrollo para TuCancha
# PHP 8.4 para mantener paridad con producción (el VPS corre php84).
FROM php:8.4-fpm

# UID/GID del usuario del host. En Linux/WSL evita que los archivos que crea
# el contenedor queden como root en el proyecto.
ARG UID=1000
ARG GID=1000

# ── Dependencias del sistema ──────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libgmp-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# ── Extensiones PHP ───────────────────────────────────────────────────
# pdo_pgsql/pgsql → PostgreSQL
# bcmath, gmp     → webpush (claves VAPID)
# intl            → formato de fechas/moneda en es-AR
# pcntl, sockets  → queue workers y Reverb (websockets)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        bcmath \
        gmp \
        intl \
        zip \
        gd \
        exif \
        pcntl \
        sockets \
        opcache

# ── Composer ──────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Config de PHP para desarrollo ─────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-tucancha.ini

# ── Usuario no-root ───────────────────────────────────────────────────
RUN groupadd -g ${GID} app 2>/dev/null || true \
    && useradd -u ${UID} -g ${GID} -m -s /bin/bash app 2>/dev/null || true \
    && mkdir -p /var/www/html \
    && chown -R ${UID}:${GID} /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
# El sed elimina CRLF por si el archivo se clonó en Windows sin .gitattributes
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint \
    && chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
USER app

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
