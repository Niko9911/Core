FROM php:7.2-apache

RUN apt-get update && apt-get install -yq --no-install-recommends \
		wget \
		vim \
		git \
		ssh \
		zip \
		unzip \
		nano \
		curl \
		python \
		gnupg2

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get install -yq \
       nodejs  \
    && npm install -g npm \
    && npm install -g yo grunt-cli bower express

RUN apt-get update && apt-get install -yq --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libicu-dev \
        libxslt-dev \
	    libpcre3-dev \
	    gcc make \
	    libmemcached-dev \
	    zlib1g-dev \
	    build-essential \
	    autoconf \
	    libssl-dev \
	    libsodium-dev \
	    libbz2-dev \
	    libyaml-dev \
	    libpq-dev \
	    libcurl4-openssl-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install \
      zip bz2 \
      pdo_mysql pdo_pgsql mysqli \
      opcache \
      gettext \
      bcmath sodium \
      curl soap \
    && pecl install yaml-2.0.2 \
    && pecl install redis-4.0.1 \
    && pecl install xdebug-2.6.0 \
    && docker-php-ext-enable redis xdebug yaml opcache soap bz2

RUN curl -LJO https://github.com/phalcon/cphalcon/archive/master.zip \
    && unzip cphalcon-master.zip \
 	&& cd cphalcon-master/build \
 	&& ./install \
	&& echo 'extension="phalcon.so"' > /usr/local/etc/php/conf.d/phalcon.ini

RUN usermod --non-unique --uid 1001 www-data
RUN groupmod -g 1001 www-data
RUN a2enmod rewrite

RUN apt-get autoremove -y && apt-get clean -y

RUN wget https://phar.phpunit.de/phpunit.phar \
    && chmod +x phpunit.phar \
    && mv phpunit.phar /usr/local/bin/phpunit

RUN wget https://raw.githubusercontent.com/colinmollenhour/modman/master/modman \
    && chmod +x modman \
    && mv modman /usr/local/bin/modman

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN echo 'export MODMAN_ENV=production' >> ~/.bashrc