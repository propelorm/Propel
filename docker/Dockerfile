FROM debian:bullseye

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -q update && apt-get -qy install eatmydata curl && curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ bullseye main" > /etc/apt/sources.list.d/php.list && \
    apt-get -q update && \
    eatmydata -- apt-get -qy install --no-install-recommends locales php8.1-sqlite php8.1-pgsql php8.1-pdo php8.1-mysql php8.1-cli php8.1-intl php8.1-xml default-mysql-client curl php8.1-curl php8.1-mbstring && \
    apt-get clean && \
    sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=en_US.UTF-8 

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/2.5.1/composer.phar && chmod 755 /usr/local/bin/composer

ENV LANG en_US.UTF-8

