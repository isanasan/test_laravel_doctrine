FROM oraclelinux:7-slim AS oracle-client

ARG release=19
ARG update=3

RUN yum -y install \
    oracle-release-el7 \
    && yum-config-manager --enable ol7_oracle_instantclient \
    && yum -y install \
    oracle-instantclient${release}.${update}-basic \
    oracle-instantclient${release}.${update}-devel \
    && rm -rf /var/cache/yum

FROM php:7.4-apache
COPY --from=oracle-client /usr/lib/oracle /usr/lib/oracle
COPY --from=oracle-client /usr/include/oracle/19.3/client64 /usr/include/oracle/19.3/client64

COPY ./tnsnames.ora /usr/lib/oracle/19.3/client64/network/admin/tnsnames.ora

ENV TNS_ADMIN /usr/lib/oracle/19.3/client64/network/admin
ENV LD_LIBRARY_PATH /usr/lib/oracle/19.3/client64/lib
ENV PATH /usr/lib/oracle/19.3/client64/bin:$PATH
ENV NLS_LANG JAPANESE_JAPAN.AL32UTF8

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    ssl-cert \
    libaio1 \
    zlib1g-dev \
    libzip-dev \
    libaio-dev \
    && apt-get clean \
    && rm -fr /var/lib/apt/lists/*

RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/lib/oracle/19.3/client64/lib \
    && docker-php-ext-install -j$(nproc) oci8 pcntl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install zip

COPY ./ssl.conf /etc/apache2/sites-available/ssl.conf

COPY ./server.crt /etc/apache2/server.crt
COPY ./server.key /etc/apache2/server.key
RUN a2enmod ssl && a2ensite ssl && a2enmod rewrite

COPY ./apache2.conf /etc/apache2/apache2.conf
COPY ./000-default.conf /etc/apache2/sites-enabled/

WORKDIR /var/www/html

RUN echo "ServerName localhost" | tee /etc/apache2/conf-available/fqdn.conf \
    && a2enconf fqdn

CMD bash -c "chmod -R 777 /var" && "apache2-foreground"
