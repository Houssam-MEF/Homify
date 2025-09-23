# ---- Composer stage
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction
COPY . .
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction

# ---- Node build stage (Tailwind/Vite)
FROM node:20 AS assets
WORKDIR /app
COPY package.json package-lock.json* yarn.lock* pnpm-lock.yaml* ./
# Use npm by default; switch to yarn/pnpm if you use those
RUN if [ -f package-lock.json ]; then npm ci; elif [ -f yarn.lock ]; then corepack enable && yarn install --frozen-lockfile; elif [ -f pnpm-lock.yaml ]; then corepack enable && pnpm install --frozen-lockfile; else npm install; fi
COPY . .
RUN npm run build

# ---- Runtime (Apache + PHP 8.2)
FROM php:8.2-apache

# System deps + PHP extensions you likely need
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql zip intl gd \
 && a2enmod rewrite headers \
 && rm -rf /var/lib/apt/lists/*

# Set Apache docroot to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copy app code
COPY . .

# Copy vendor from Composer stage
COPY --from=vendor /app/vendor ./vendor

# Copy built assets from Node stage
COPY --from=assets /app/public/build ./public/build

# Ensure storage writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Entrypoint: do one-time Laravel setup then start Apache
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/entrypoint.sh"]
