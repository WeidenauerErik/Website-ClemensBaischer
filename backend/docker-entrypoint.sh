#!/bin/sh
set -e

# Wait for the database to accept connections before running migrations.
if [ -n "$DATABASE_URL" ]; then
    echo "Waiting for the database..."
    ATTEMPTS=0
    until php bin/console dbal:run-sql "SELECT 1" >/dev/null 2>&1; do
        ATTEMPTS=$((ATTEMPTS + 1))
        if [ "$ATTEMPTS" -ge 30 ]; then
            echo "Database not reachable after 30 attempts, giving up."
            exit 1
        fi
        sleep 2
    done

    echo "Running database migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

exec "$@"
