#!/bin/bash

# Start redis server in the background
redis-server --daemonize yes
redis-cli CONFIG SET maxmemory 1gb
redis-cli CONFIG GET maxmemory
php artisan route:clear
php artisan route:cache
php artisan optimize:clear

php artisan migrate
php artisan storage:link


# php artisan l5-swagger:generate
# prism mock storage/api-docs/api-docs.json --port 4000

# ============================================
# Cron is now managed by Supervisor
# ============================================
# echo "Starting crond"
# service cron start
# service cron status
# (crontab -l -u www-data ; echo "*/5 * * * * echo 'test log' > /var/www/html/test-log.log 2>&1") | crontab -u www-data -
# (crontab -l -u www-data ; echo "*/5 * * * * /usr/local/bin/php /var/www/html/artisan command:update_cache > /var/www/html/cronjob.log 2>&1") | crontab -u www-data -
# (crontab -l -u www-data ; echo "*/5 * * * * /usr/local/bin/php /var/www/html/artisan command:update_weather > /var/www/html/weather.log 2>&1") | crontab -u www-data -

echo "Setup completed"

# Write version if command exists
if php artisan list | grep -q "app:write_version"; then
    php artisan app:write_version
else
    echo "Skipping app:write_version (command not found)"
fi

# ============================================
# Queue workers are now managed by Supervisor
# ============================================
# nohup php artisan queue:listen --timeout=1800 > /var/www/html/storage/logs/queue.log 2>&1 &

# ============================================
# Apache is now managed by Supervisor
# ============================================
# echo "Starting Apache"
# apache2-foreground

# Start supervisord (manages Apache, Queue workers, and Cron)
echo "Starting Supervisord"
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
