#!/bin/sh
less_than_php_5_3=$(php -r "echo version_compare(PHP_VERSION, '5.3.0', '<') ? 'TRUE' : 'FALSE';")
if [ ${less_than_php_5_3} = 'TRUE' ]
then
  pear channel-discover pear.phing.info
  pear install phing/phing
  phpenv rehash
fi
