#!/bin/env bash

if [[ -e "./database/database.db" ]]; then
    echo "Database already exists"
    exit 1
fi

sqlite3 ./database/database.db < ./database/database.sql
sqlite3 ./database/database.db < ./database/populate.sql
echo "Database created"
