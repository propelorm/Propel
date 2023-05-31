#!/usr/bin/env bash
# Reset Propel tests fixtures
# 2011 - William Durand <william.durand1@gmail.com>


cd "$(dirname "$0")"


function rebuild
{
    local dir=$1
    local project=$2

    echo "[ $dir ]"

    if [ -d "$dir/build" ] ; then
        rm -rf "$dir/build"
    fi

    echo "Building : $dir "

    cd $dir
    $ROOT/generator/bin/propel-gen -Dproject=$project -Dproject.dir=. main 
    $ROOT/generator/bin/propel-gen -Dproject=$project -Dproject.dir=. insert-sql
}

ROOT="$(pwd)/../"
FIXTURES_DIR="$(pwd)/fixtures"

mysql -u root -h mysql -e 'SET FOREIGN_KEY_CHECKS = 0; DROP DATABASE IF EXISTS test; DROP SCHEMA IF EXISTS second_hand_books; DROP SCHEMA IF EXISTS contest; DROP DATABASE IF EXISTS reverse_bookstore; DROP SCHEMA IF EXISTS bookstore_schemas; SET FOREIGN_KEY_CHECKS = 1;'
mysql -u root -h mysql -e 'CREATE DATABASE test; CREATE SCHEMA bookstore_schemas; CREATE SCHEMA contest; CREATE SCHEMA second_hand_books; CREATE DATABASE reverse_bookstore; CREATE DATABASE bookstore_schemas;'

rebuild $FIXTURES_DIR/bookstore bookstore
rebuild $FIXTURES_DIR/bookstore-packaged bookstore
rebuild $FIXTURES_DIR/nestedset nestedset
rebuild $FIXTURES_DIR/treetest treetest
rebuild $FIXTURES_DIR/namespaced bookstore_namespaced

echo "Building reverse thingys..."

cd $ROOT/test/fixtures/reverse/mysql
$ROOT/generator/bin/propel-gen -Dproject=reverse_bookstore -Dproject.dir=. insert-sql
cd $ROOT

echo "Reset complete."

