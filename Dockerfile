FROM ubuntu:24.04

## for apt to be noninteractive
# ENV DEBIAN_FRONTEND noninteractive

## for pecl to be noninteractive
# printf "\n" | pecl install package_name

RUN apt-get update && \
apt-get -y install software-properties-common && \
add-apt-repository -y ppa:ondrej/php && \
DEBIAN_FRONTEND=noninteractive apt-get -yq install php8.3 && \
apt-get -y install php8.3-curl \
                    php8.3-cli \
                    php-json \
                    php8.3-mbstring \
                    php8.3-xml \
                    php8.3-pcov \
                    php8.3-xdebug && \
apt-get -y install git


# install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# project copy
COPY . /var/www/html

# project
WORKDIR /var/www/html

RUN composer install -n --ignore-platform-reqs
