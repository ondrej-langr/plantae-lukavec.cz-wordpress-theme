# Use the official WordPress image as the base
FROM wordpress:php8.4-apache

# Install required tools and dependencies
RUN apt-get update && apt-get install -y \
    less \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Install Mysql
RUN apt-get update && \
    apt-get install -y default-mysql-client && \
    rm -rf /var/lib/apt/lists/*

# Set working directory to WordPress root
WORKDIR /var/www/html

# Expose port 80 for the web server
EXPOSE 80

# Add entrypoint script to automate WordPress setup using WP-CLI
COPY wpcli.entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/wpcli.entrypoint.sh

# Copy wordpress installation before anything starts, this is because we execute wp-cli afterwards
# RUN mv /usr/src/wordpress/ /var/www/html/ # RUN php -d memory_limit=512M "$(which wp)" core download --path=/var/www/html --locale=cs_CZ --allow-root

# Use the custom entrypoint script
ENTRYPOINT ["wpcli.entrypoint.sh"]
