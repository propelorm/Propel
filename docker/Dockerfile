FROM debian:bullseye
USER root
RUN apt-get -q update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y locales php7.4 php7.4-sqlite php7.4-pgsql php7.4-pdo php7.4-mysql php7.4-cli php7.4-intl php7.4-xml default-mysql-client curl php7.4-curl && \
    sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=en_US.UTF-8 
RUN adduser docker 

USER docker
ENV LANG en_US.UTF-8

