# Stage 1: Build frontend assets
FROM node:24-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY . .

COPY .env.production .env.production
RUN npm run build && rm .env.production

# Stage 2: Production Container
FROM dunglas/frankenphp:latest
WORKDIR /app

RUN install-php-extensions pdo_mysql gd

COPY . .
COPY --from=frontend /app/public/build public/build
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-progress

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV SERVER_NAME=:8080
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV MAX_REQUESTS=500

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
ENTRYPOINT ["entrypoint.sh"]
