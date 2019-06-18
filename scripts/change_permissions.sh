#!/bin/bash
chown -R ac2-user:apache /var/www/
chmod 2775 /var/www

#find /var/www -type d -exec sudo chmod 2775 {} \;
#find /var/www -type f -exec sudo chmod 0664 {} \;
#chmod -R 777 /var/www/html/advuser/frontend/web/assets/
#chmod -R 777 /var/www/html/advuser/frontend/runtime/
#chmod -R 777 /var/www/html/advuser/backend/web/assets/
#chmod -R 777 /var/www/html/advuser/backend/runtime/
#chmod -R 777 /var/www/html/advuser/api/web/assets/
#chmod -R 777 /var/www/html/advuser/api/runtime/
