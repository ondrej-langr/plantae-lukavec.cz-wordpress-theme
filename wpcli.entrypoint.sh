#!/bin/bash

# Start Apache in the background
apache2-foreground &

# Wait for MySQL to be ready
HOST=$(echo $WORDPRESS_DB_HOST | cut -d: -f1)
PORT=$(echo $WORDPRESS_DB_PORT | cut -d: -f2)

CMD=$@

until mysql -h $HOST -P $PORT -D $WORDPRESS_DB_NAME -u "$WORDPRESS_DB_USER" -p$WORDPRESS_DB_PASSWORD -e '\q'; do
  >&2 echo "Mysql is unavailable - sleeping..."
  sleep 2
done

mysql -D $WORDPRESS_DB_NAME -h $HOST -u $WORDPRESS_DB_USER -p$WORDPRESS_DB_PASSWORD < /var/www/backup.sql

php -d memory_limit=512M "$(which wp)" core download --path=/var/www/html --locale=cs_CZ --allow-root

php -d memory_limit=512M "$(which wp)" config create --dbhost=$HOST --dbname="$WORDPRESS_DB_NAME" --dbuser=$WORDPRESS_DB_USER --dbpass=$WORDPRESS_DB_PASSWORD --dbprefix=zahr_lukavec --allow-root

# Install WordPress using WP-CLI
# wp core install --allow-root --path="/var/www/html" --url="http://localhost:8080" --title="Okrasné a užitkové rostliny" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"

# Replace old with new after migration from production database
wp search-replace https://plantae-lukavec.cz http://localhost:8080 --all-tables --allow-root

wp user update admin --user_pass="password" --allow-root

wp plugin install --activate --allow-root \
    contact-form-7 \
    woocommerce \
    custom-payment-gateways-woocommerce \
    easy-woocommerce-auto-sku-generator \
    easy-watermark \
    handmade-woocommerce-order-status-control \
    loco-translate \
    plausible-analytics \
    woo-preview-emails \
    shopmagic-for-woocommerce \
    shopmagic-for-woocommerce \
    lightweight-grid-columns \
    woocommerce-easy-table-rate-shipping \
    visual-term-description-editor \
    woocommerce-services

wp plugin uninstall hello-dolly akismet --alow-root

wp theme delete twentytwentyfive --alow-root

wp config set --raw --alow-root WP_DEBUG true
wp config set --raw --alow-root WP_DEBUG_LOG true
wp config set --raw --alow-root WP_DEBUG_DISPLAY false

wp theme activate zahradnictvi --allow-root

# Keep the container running
tail -f /dev/null
