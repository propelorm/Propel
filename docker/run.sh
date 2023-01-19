#!/usr/bin/env bash

set -x

id

/bin/bash docker/reset.sh

curl -o composer https://getcomposer.org/download/2.5.1/composer.phar
chmod 755 composer

./composer install -n
bash test/reset_tests.sh
