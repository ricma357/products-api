#!/bin/bash



cd /var/www/html/
# make sure you remove if cache existed when image was build
php bin/console cache:clear --no-warmup
# execute migrations if they exist
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php bin/console doctrine:fixtures:load


# run apache after all is finished
/usr/local/bin/docker-php-entrypoint apache2-foreground

