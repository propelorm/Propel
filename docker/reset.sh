#!/usr/bin/env bash

mysql -u root -e 'SET FOREIGN_KEY_CHECKS = 0; DROP DATABASE IF EXISTS test; DROP SCHEMA IF EXISTS second_hand_books; DROP SCHEMA IF EXISTS contest; DROP DATABASE IF EXISTS reverse_bookstore; DROP SCHEMA IF EXISTS bookstore_schemas; SET FOREIGN_KEY_CHECKS = 1;'
mysql -u root -e 'CREATE DATABASE test; CREATE SCHEMA bookstore_schemas; CREATE SCHEMA contest; CREATE SCHEMA second_hand_books; CREATE DATABASE reverse_bookstore;'
