#!/bin/env bash

rm ./database/database.db
bash ./create_db.sh

php -S localhost:9000
