FROM scratch

ADD rootfs.tar.xz /
CMD ["bash"]

RUN printf 'APT::Install-Recommends "0";\nAPT::Install-Suggests "0";\n' > /etc/apt/apt.conf.d/01norecommend
RUN apt-get update -y
RUN apt-get install -y sysv-rc-conf vim curl
RUN apt-get install -y nginx-extras
RUN apt-get install -y make libvips-dev
RUN apt-get install -y php7.0 php7.0-dev php7.0-fpm php7.0-gd php7.0-curl php7.0-sqlite php7.0-mbstring php-pear

RUN curl -ksS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN yes '' | pecl install -f vips
RUN echo "extension=vips.so" > /etc/php/7.0/mods-available/vips.ini
RUN phpenmod vips

RUN sysv-rc-conf nginx on
RUN sysv-rc-conf php7.0-fpm on

RUN /etc/init.d/nginx restart
RUN /etc/init.d/php7.0-fpm restart
RUN /etc/init.d/php7.0-fpm start

EXPOSE 80