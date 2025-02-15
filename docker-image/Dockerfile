FROM orangehrm/prod-environment:ubuntu24.04-php8.3-latest

LABEL authors = "OrangeHRM TechOps <techops@orangehrm.com>"

# install dependent software
RUN apt install build-essential \
    &&  gcc --version

RUN sh -c 'echo "deb http://opensource.wandisco.com/ubuntu focal svn19" >> /etc/apt/sources.list.d/subversion19.list'
RUN wget -q http://opensource.wandisco.com/wandisco-debian-new.gpg -O- |  apt-key add -
RUN apt-get update

#install Subverson
RUN apt-get install -y subversion

# Replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN mkdir /root/.nvm
ENV NVM_DIR /root/.nvm
ENV NODE_VERSION_D 6
ENV NODE_VERSION_L 14.17.5

#installing nodejs
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.34.0/install.sh | bash \
    && . $NVM_DIR/nvm.sh \  
    && nvm install $NODE_VERSION_D \
    && nvm alias default $NODE_VERSION_D \
    && nvm install $NODE_VERSION_L \
    && nvm alias latest $NODE_VERSION_L \
    && nvm install 18.18.0 \
    && nvm alias hydrogen 18.18.0 \
    && nvm ls \
    && nvm use default \ 
    && npm install bower -g \ 
    && npm install -g gulp@4.0.2 \
    && npm install nodemon -g \
    && npm install pm2 -g

ENV NODE_PATH_D $NVM_DIR/v$NODE_VERSION_D/lib/node_modules
ENV PATH_D      $NVM_DIR/v$NODE_VERSION_D/bin:$PATH

ENV NODE_PATH_L $NVM_DIR/v$NODE_VERSION_L/lib/node_modules
ENV PATH_L      $NVM_DIR/v$NODE_VERSION_L/bin:$PATH

RUN apt install -y \
    bzip2 \
    git \
    nmap \
    sendmail \
    libapache2-mod-svn \
    vim \
    && apt-get autoclean \
    && apt-get install --only-upgrade bash

# Our user in the container
USER root
# set working dir as the installer directory
WORKDIR /var/www/html/installer/

# set git system config to use https instead of git
RUN git config --system url."https://".insteadOf git://

#install bower and gulp
#RUN npm install bower gulp nodemon pm2 -g
#RUN npm install -f node-sass

#install wkhtmltopdf (0.12.6)
# RUN wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.focal_amd64.deb
# RUN apt install -y ./wkhtmltox_0.12.6-1.focal_amd64.deb
# RUN rm -r wkhtmltox_0.12.6-1.focal_amd64.deb
RUN apt install -y wkhtmltopdf

#update OSLON Timezone DB
RUN pecl install timezonedb
RUN echo "extension=timezonedb.so" >> /etc/php/8.3/apache2/php.ini
RUN echo "extension=timezonedb.so" >> /etc/php/8.3/cli/php.ini

# # install php-ast module
RUN pecl install ast
RUN echo "extension=ast.so" >> /etc/php/8.3/apache2/php.ini
RUN echo "extension=ast.so" >> /etc/php/8.3/cli/php.ini

# #install php-stats module
# RUN pecl install stats-2.0.3
# RUN echo "extension=stats.so" >> /etc/php/8.3/apache2/php.ini
# RUN echo "extension=stats.so" >> /etc/php/8.3/cli/php.ini


# # temporary switch on the "allow_url_fopen" to install composer and phan
RUN sed -i 's:^allow_url_fopen = Off$:allow_url_fopen = On:g' /etc/php/8.3/apache2/php.ini
RUN sed -i 's:^allow_url_fopen = Off$:allow_url_fopen = On:g' /etc/php/8.3/cli/php.ini

# installing the  composer 2.x 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.7.8

#install mod_vhost_alias
RUN a2enmod vhost_alias

#Enable mod_expire module
RUN a2enmod expires

#add vhost config
COPY config/apache2-sites/orangehrm.conf /etc/apache2/sites-available/orangehrm.conf
COPY config/apache2-sites/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/cert/orangehrm.crt /etc/apache2/cert/orangehrm.crt
COPY config/cert/orangehrm.key /etc/apache2/cert/orangehrm.key
RUN rm /etc/apache2/sites-enabled/000-default.conf
RUN ln -s /etc/apache2/sites-available/orangehrm.conf /etc/apache2/sites-enabled/orangehrm.conf
RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf


# copy configuration files
COPY config/mysql-client/my.cnf /etc/mysql/my.cnf
COPY config/mysql-client/conf.d /etc/mysql/conf.d
COPY config/db-check.sh /var/www/db-check.sh

COPY phpunit-8.5.41.phar /usr/bin/phpunit8
COPY phpunit-9.6.22.phar /usr/bin/phpunit9
COPY phpunit-10.5.39.phar /usr/bin/phpunit10
COPY phpunit-11.5.1.phar /usr/bin/phpunit11

RUN  chmod +x /usr/bin/phpunit8
RUN  chmod +x /usr/bin/phpunit9
RUN  chmod +x /usr/bin/phpunit10
RUN  chmod +x /usr/bin/phpunit11

# installing phan
RUN cd /var/lib && composer require --dev phan/phan
RUN ln -s /var/lib/vendor/bin/phan /usr/bin/phan

# temporary switch on the "allow_url_fopen" to install composer and phan
RUN sed -i 's:^allow_url_fopen = On$:allow_url_fopen = Off:g' /etc/php/8.3/apache2/php.ini
RUN sed -i 's:^allow_url_fopen = On$:allow_url_fopen = Off:g' /etc/php/8.3/cli/php.ini

#installing nc command
# RUN apt install -y netcat
RUN apt install -y netcat-traditional

# Generating the report of installed packages
RUN apt list --installed > /var/log/installed_packages_detailed_report.txt