#!/usr/bin/env bash
cd docker
docker-compose up -d db
sleep 20
docker-compose exec -T db bash < reset.sh
docker-compose build
docker-compose run php
