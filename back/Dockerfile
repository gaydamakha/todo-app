FROM php:8.2.2-fpm-alpine3.16

# Install packages
RUN apk upgrade && apk --no-cache add php8-json php8-openssl php8-curl php8-tokenizer\
    php8-zlib php8-xml php8-xmlwriter php8-phar php8-intl php8-dom php8-xmlreader php8-simplexml\
    php8-ctype php8-session php8-mbstring php8-gd php8-pear php8-dev php8-fileinfo php8-iconv php8-sodium \
    libressl-dev nginx supervisor curl gcc musl-dev make openssl

RUN pecl install mongodb

RUN echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongo.ini

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
# Remove default server definition

# Configure PHP-FPM
COPY config/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY config/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

# Install composer
# Register the COMPOSER_HOME environment variable
ENV COMPOSER_HOME /composer

# Add global binary directory to PATH and make sure to re-export it
ENV PATH /composer/vendor/bin:$PATH

# Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Setup the Composer installer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }"

RUN php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot && rm -rf /tmp/composer-setup.php

# Setup document root
RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY --chown=nobody . /var/www/html

# Install dependencies
COPY composer.json /var/www/html
RUN composer install

RUN chown -R nobody:nobody /var/www/html

# Switch to use a non-root user from here on
USER nobody

# Generate keys for JWT
#RUN mkdir -p /var/www/html/config/jwt
#RUN openssl genpkey -out /var/www/html/config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:lalalala
#RUN openssl pkey -in /var/www/html/config/jwt/private.pem -out /var/www/html/config/jwt/public.pem -pubout -passin pass:lalalala

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
