#!/bin/bash

cat > /etc/apache2/sites-available/000-default.conf << 'EOF'
<VirtualHost *:8080>
    DocumentRoot /home/site/wwwroot/public

    <Directory /home/site/wwwroot/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog /home/LogFiles/apache_error.log
    CustomLog /home/LogFiles/apache_access.log combined
</VirtualHost>
EOF

a2enmod rewrite
service apache2 restart

cd /home/site/wwwroot

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true
php artisan migrate --force --no-interaction