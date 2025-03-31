FROM php:8.2.0-apache

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ADD docker/php/php.ini "$PHP_INI_DIR/php.ini"

# In case we are using dbo.Employees table from sensus
# then code below is needed to remove error SQLSTATE[08001]: [Microsoft][ODBC Driver 17 for SQL Server]SSL Provider: [error:1425F102:SSL routines:ssl_choose_client_version:unsupported protocol]
RUN sed -i 's/MinProtocol = TLSv1.2/MinProtocol = TLSv1/g' /etc/ssl/openssl.cnf
RUN sed -i 's/MinProtocol = TLSv1.2/MinProtocol = TLSv1/g' /usr/lib/ssl/openssl.cnf
RUN sed -i 's/DEFAULT@SECLEVEL=2/DEFAULT@SECLEVEL=1/g' /etc/ssl/openssl.cnf
RUN sed -i 's/DEFAULT@SECLEVEL=2/DEFAULT@SECLEVEL=1/g' /usr/lib/ssl/openssl.cnf

# This is needed for Headless Chrome (installs node and chromium packages)
RUN curl -sL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils libgbm-dev wget

# Install Microsoft fonts for usages in pdf generation (unclear about license)
RUN echo "deb http://deb.debian.org/debian buster main contrib" >> /etc/apt/sources.list
RUN apt-get update && apt-get install -y ttf-mscorefonts-installer

# Install libraries required for PHP extensions
RUN apt-get update && apt-get install -y \
	libldb-dev \
	libldap2-dev \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libz-dev \
	libzip-dev \
    gnupg \
    cron \
	;

# Install php extensions
RUN docker-php-ext-install \
    pcntl \
    ldap \
    opcache \
    zip \
    exif \
    mysqli \
    pdo \
    pdo_mysql \
    ;

# Install php gd extension
RUN apt-get update && apt-get install -y libjpeg-dev libpng-dev
RUN docker-php-ext-configure gd --enable-gd --with-jpeg
RUN docker-php-ext-install gd

## Installing MS SQL drivers
ENV ACCEPT_EULA=Y
RUN apt-get update && apt-get install -y gnupg2
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update
RUN apt-get install -y unixodbc-dev=2.3.7 unixodbc=2.3.7 odbcinst1debian2=2.3.7 odbcinst=2.3.7
RUN ACCEPT_EULA=Y apt-get -y --no-install-recommends install mssql-tools msodbcsql17

# Install sqlsrv
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install Composeer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ../Downloads/poa-safety-management-system-main /var/www/html/
RUN cd /var/www/html && /usr/bin/composer install --no-dev

# Install Supervisor
RUN apt-get update && apt-get install -y openssh-server supervisor
RUN mkdir -p /var/lock/apache2 /var/run/apache2 /var/run/sshd /var/log/supervisor

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install YARN, (Node installed at the top with chromium packages)
RUN npm i -g yarn

WORKDIR /var/www/html

RUN yarn install
RUN npx tailwindcss -o ./resources/css/build.css --minify
RUN npm run build

#Migrate database, optimize and cache laravel app
#RUN php artisan view:cache
#RUN php artisan event:cache
#RUN php artisan route:cache

#currently running these commands through nomad, because in build step there is no .env file
#RUN php artisan config:cache
#RUN php artisan migrate --force
#RUN rm /var/www/html/.env
#RUN chmod o+w ./storage/ -R

ADD ./docker/apache2/apache2.conf /etc/apache2/apache2.conf
COPY ./docker/apache2/app.conf /etc/apache2/sites-available/app.conf
RUN a2dissite 000-default.conf
RUN a2ensite app.conf \
    && chmod -R 777 storage \
    && a2enmod rewrite
RUN a2enmod ssl

# CRON
ADD docker/cron/crontab /etc/cron.d/crontab

RUN chmod 0644 /etc/cron.d/crontab

RUN crontab /etc/cron.d/crontab

RUN touch /var/log/cron.log

#RUN chmod o+w ./storage/ -R

CMD supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
