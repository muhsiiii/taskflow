# Multi-stage build: node build for assets, php runtime for app

### Node build stage
FROM node:22-bookworm AS node-build
WORKDIR /app
COPY package.json package-lock.json* ./
COPY vite.config.js ./
COPY resources resources
RUN npm ci --prefer-offline --no-audit --progress=false
RUN npm run build

### PHP runtime stage (slim, bookworm)
FROM php:8.2-fpm-bookworm-slim
WORKDIR /var/www/html

# system deps (non-interactive, no-install-recommends to keep image small)
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev \
    libonig-dev \
    zip \
    curl \
    ca-certificates \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath intl gd sockets xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy app

# copy application source
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

# Copy entrypoint script and make it executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use entrypoint to clear caches at container start, then run the server
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
