FROM scratch

ADD rootfs.tar.xz /
CMD ["bash"]

RUN printf 'APT::Install-Recommends "0";\nAPT::Install-Suggests "0";\n' > /etc/apt/apt.conf.d/01norecommend
RUN apt-get update -y
RUN apt-get install -y sysv-rc-conf vim curl git make dirmngr apt-transport-https ca-certificates curl software-properties-common
RUN update-ca-certificates
RUN apt-get install -y nginx-extras
RUN apt-get install -y php7.0 php7.0-dev php7.0-fpm php7.0-gd php7.0-curl php7.0-sqlite php7.0-mbstring php-pear

# composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -ksS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# libvips
RUN apt-get install -y \
    cdbs debhelper dh-autoreconf flex bison \
    libjpeg-dev libtiff-dev libpng-dev libgif-dev librsvg2-dev libpoppler-glib-dev zlib1g-dev fftw3-dev liblcms2-dev \
    liblcms2-dev libmagickwand-dev libfreetype6-dev libpango1.0-dev libfontconfig1-dev libglib2.0-dev libice-dev \
    gettext pkg-config libxml-parser-perl libexif-gtk-dev liborc-0.4-dev libopenexr-dev libmatio-dev libxml2-dev \
    libcfitsio-dev libopenslide-dev libwebp-dev libgsf-1-dev libgirepository1.0-dev gtk-doc-tools
RUN cd /tmp && \
    curl -L -O https://github.com/libvips/libvips/releases/download/v8.7.4/vips-8.7.4.tar.gz && \
    tar zxvf vips-8.7.4.tar.gz && \
    cd vips-8.7.4 && \
    ./configure --enable-debug=no --without-python && \
    make && \
    make install && \
    ldconfig && \
    cd .. && \
    rm ./vips-8.7.4.tar.gz ./vips-8.7.4 -rf
RUN yes '' | pecl install -f vips
RUN echo "extension=vips.so" > /etc/php/7.0/mods-available/vips.ini
RUN phpenmod vips

# mysql
# RUN apt-get install -y mariadb-server php7.0-mysql
# RUN sys-rc-conf mysql on
# RUN /etc/init.d/mysql restart

# xhprof for debug
RUN curl "https://github.com/tideways/php-xhprof-extension/archive/v5.0-beta3.tar.gz" -kfsL -o ./v5.0-beta3.tar.gz  && \
    tar zxvf ./v5.0-beta3.tar.gz && \
    cd ./php-xhprof-extension-5.0-beta3 && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    cd .. && rm -rf ./v5.0-beta3.tar.gz ./php-xhprof-extension-5.0-beta3
RUN echo "extension=tideways_xhprof.so" > /etc/php/7.0/mods-available/tideways_xhprof.ini
RUN phpenmod tideways_xhprof
RUN mkdir /tmp/xhprof && chmod 777 /tmp/xhprof
RUN cd /var/www && \
    git clone https://github.com/sters/xhprof-html.git

RUN sysv-rc-conf nginx on
RUN sysv-rc-conf php7.0-fpm on

RUN /etc/init.d/nginx restart
RUN /etc/init.d/php7.0-fpm restart
RUN /etc/init.d/php7.0-fpm start

EXPOSE 80 8000