FROM alpine:3.10

# Install packages
RUN apk --no-cache add php7 php7-fpm php7-json php7-openssl php7-curl php7-tokenizer\
    php7-zlib php7-xml 	php7-xmlwriter php7-phar php7-intl php7-dom php7-xmlreader php7-simplexml\
    php7-ctype php7-session php7-mbstring php7-gd php7-pear php7-dev php7-fileinfo php7-iconv\
    libressl-dev nginx supervisor curl gcc musl-dev make python g++

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
# Remove default server definition
RUN rm /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY config/php.ini /etc/php7/conf.d/custom.ini

# Configure supervisord
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/tmp/nginx && \
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

#Install npm
RUN apk --update add npm

# Setup document root
RUN mkdir -p /var/www/html
WORKDIR /var/www/html/
COPY --chown=nobody . /var/www/html/

# Install dependencies
COPY package.json /var/www/html/package.json
RUN npm config set registry https://registry.npmjs.org/ && npm install
RUN npm run build
COPY composer.json /var/www/html/composer.json
RUN composer install

RUN chown -R nobody:nobody /var/www/html

# Switch to use a non-root user from here on
USER nobody

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping
