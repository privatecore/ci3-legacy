ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

# application dependencies
RUN apt-get update -q -y \
	&& apt-get install -q -y --no-install-recommends \
		git \
		curl \
		imagemagick \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmagickwand-dev \
		libpng-dev \
		zip \
		unzip \
	&& pecl install imagick \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install mysqli \
	&& docker-php-ext-enable imagick

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# cleanup
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# configure non-root user
ARG PUID
ARG PGID
RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data
