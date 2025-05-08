#!/bin/sh
set -e

php bin/console c:c

# Exécution des migrations SQL
php bin/console sql-migrations:execute

# Ajout de la table pour transport
php bin/console messenger:setup-transports

# Tester la commande
php bin/console import-recipients public/recipients.csv

exec php-fpm