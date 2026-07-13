FROM wordpress:php8.3-apache

# WP-CLI — supaya `wp` selalu tersedia di dalam container
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# .htaccess — wajib untuk permalink cantik & REST API
RUN printf '%s\n' \
    '# BEGIN WordPress' \
    '<IfModule mod_rewrite.c>' \
    'RewriteEngine On' \
    'RewriteBase /' \
    'RewriteRule ^index\.php$ - [L]' \
    'RewriteCond %{REQUEST_FILENAME} !-f' \
    'RewriteCond %{REQUEST_FILENAME} !-d' \
    'RewriteRule . /index.php [L]' \
    '</IfModule>' \
    '# END WordPress' \
    > /usr/src/wordpress/.htaccess \
    && chown www-data:www-data /usr/src/wordpress/.htaccess

# Buang plugin bawaan yang tidak dipakai
RUN rm -rf /usr/src/wordpress/wp-content/plugins/hello.php \
           /usr/src/wordpress/wp-content/plugins/akismet
