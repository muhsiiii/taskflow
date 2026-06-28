# Multi-stage build: node build for assets, php runtime for app

### Node build stage
FROM node:22-bookworm AS node-build
WORKDIR /app
COPY package.json package-lock.json* ./
COPY vite.config.js ./
COPY resources resources
RUN npm ci --prefer-offline --no-audit --progress=false
RUN npm run build

### PHP runtime stage
FROM php:8.2-cli
WORKDIR /var/www/html

# system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    libonig-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath intl gd sockets xml && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy app
COPY . /var/www/html

# install php deps using lockfile
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# copy built assets from node stage
COPY --from=node-build /app/public/build /var/www/html/public/build

# set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

ENV APP_ENV=production
ENV APP_DEBUG=false

# Expose port (Railway sets PORT env var)
EXPOSE 8000

# Start the app using artisan serve on container start
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
