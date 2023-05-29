#!/usr/bin/env bash
# Reset Propel tests fixtures
# 2011 - William Durand <william.durand1@gmail.com>


cd "$(dirname "$0")"

CURRENT=`pwd`

function rebuild
{
    local dir=$1

    echo "[ $dir ]"

    if [ -d "$dir/build" ] ; then
        rm -rf "$dir/build"
    fi

    $ROOT/generator/bin/propel-gen $FIXTURES_DIR/$dir main > /dev/null
    $ROOT/generator/bin/propel-gen $FIXTURES_DIR/$dir insert-sql > /dev/null
}

ROOT_DIR=""
FIXTURES_DIR=""

if [ -d "$CURRENT/fixtures" ] ; then
    ROOT=".."
    FIXTURES_DIR="$CURRENT/fixtures"
elif [ -d "$CURRENT/test/fixtures" ] ; then
    ROOT="."
    FIXTURES_DIR="$CURRENT/test/fixtures"
else
    echo "ERROR: No 'test/fixtures/' directory found !"
    exit 1
fi


# Special case for reverse fixtures

REVERSE_DIRS=`ls $FIXTURES_DIR/reverse`

for dir in $REVERSE_DIRS ; do
    if [ -f "$FIXTURES_DIR/reverse/$dir/build.properties" ] ; then
        echo "Building reverse for: $dir "
        $ROOT/generator/bin/propel-gen $FIXTURES_DIR/reverse/$dir insert-sql > /dev/null
    fi
done

