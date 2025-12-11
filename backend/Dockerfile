FROM php:8.2-fpm

WORKDIR /var/www

# Dependências do sistema necessárias para composer e extensões comuns
RUN apt-get update \
    && apt-get install -y git unzip libzip-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Copia apenas os manifests primeiro para usar cache ao rodar composer
COPY composer.json composer.lock ./

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências sem executar os scripts (evita exigir 'artisan' antes de copiar o código)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copia o resto do código (assume que o build context é a pasta `backend`)
COPY . .

# Após copiar o código rodamos os scripts necessários e otimizamos o autoload
RUN composer dump-autoload --optimize \
    && if [ -f artisan ]; then php artisan package:discover --ansi || true; fi

EXPOSE 9000

# Host do container root/gateway ao qual este serviço se conecta
ENV GATEWAY_HOST=gateway

CMD ["php-fpm"]
