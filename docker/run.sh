#!/usr/bin/env bash
docker compose up -d 
sleep 20
docker compose run db /bin/bash reset.sh
docker compose run php
