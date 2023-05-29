#!/usr/bin/env bash

set -x
id
pwd
cd $(dirname $0) 

env


mysql -u root -h mysql -e 'SET FOREIGN_KEY_CHECKS = 0; DROP DATABASE IF EXISTS test; DROP SCHEMA IF EXISTS second_hand_books; DROP SCHEMA IF EXISTS contest; DROP DATABASE IF EXISTS reverse_bookstore; DROP SCHEMA IF EXISTS bookstore_schemas; SET FOREIGN_KEY_CHECKS = 1;'
mysql -u root -h mysql -e 'CREATE DATABASE test; CREATE SCHEMA bookstore_schemas; CREATE SCHEMA contest; CREATE SCHEMA second_hand_books; CREATE DATABASE reverse_bookstore; CREATE DATABASE bookstore_schemas;'

for file in ../test/fixtures/bookstore/build/sql/*.sql test/fixtures/schemas/build/sql/schema.sql
do
    echo "Loading : $file "
    cat $file | mysql -u root -h mysql test
done

echo "test reset"

bash -x ../test/reset_tests.sh
