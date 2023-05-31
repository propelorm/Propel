#!/usr/bin/env bash

set -x
id
pwd
cd $(dirname $0) 

env

bash -x ../test/reset-tests.sh
