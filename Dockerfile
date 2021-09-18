FROM laravelsail/php74-composer:latest
RUN docker-php-ext-install pdo pdo_mysql exif
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
RUN set -xe; \
	apt-get -y --no-install-recommends install g++ zlib1g-dev; \
	pecl install grpc; \
	docker-php-ext-enable grpc
COPY custom-php.ini /usr/local/etc/php/conf.d/
WORKDIR /app
COPY . /app
RUN composer update
# RUN composer update
