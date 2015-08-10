FROM alpine:3.2
MAINTAINER Jason Nall <jason@dreamhearth.org>

# System setup & requirements
RUN apk update && \
    apk add nginx supervisor php php-fpm php-mysql php-gd php-pdo php-pdo_mysql php-apcu php-intl php-imap php-mcrypt php-pspell php-pear gzip && \
    rm -rf /var/cache/apk/* /etc/init.d/nginx /etc/init.d/php-fpm

# Configure nginx
COPY ./conf/nginx.conf /etc/nginx/nginx.conf

# Configure php-fpm
RUN sed -i 's/;daemonize = yes/daemonize = no/g' /etc/php/php-fpm.conf && \
    sed -i 's/user = nobody/user = root/g' /etc/php/php-fpm.conf
    #sed -i 's/group = nobody/group = root/g' /etc/php/php-fpm.conf

# Install supervisor
RUN mkdir -p /var/log/supervisor
COPY ./conf/supervisord.conf /etc/supervisord.conf

# RUN cat /etc/php/php-fpm.conf
USER root
CMD ["/usr/bin/supervisord"]
