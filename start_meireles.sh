#!/bin/bash
# Meireles Connect bootstrap — ensures PHP + MariaDB are installed & running,
# then starts the PHP built-in web server. Idempotent: safe to run on every supervisor start.
set -e
LOG=/var/log/meireles_bootstrap.log
exec >> "$LOG" 2>&1
echo "==== $(date) bootstrap start ===="

export DEBIAN_FRONTEND=noninteractive

# 1. Install PHP + MariaDB if missing
if ! command -v php >/dev/null 2>&1; then
    echo "Installing PHP + MariaDB..."
    apt-get update -qq
    apt-get install -y -qq php php-cli php-mysql php-mbstring php-xml php-curl php-gd php-zip mariadb-server >/dev/null
fi

# 2. Ensure MariaDB is running
if ! command -v mysqladmin >/dev/null 2>&1 || ! mysqladmin ping >/dev/null 2>&1; then
    echo "Starting MariaDB..."
    mkdir -p /var/run/mysqld /var/log/mysql
    chown -R mysql:mysql /var/run/mysqld /var/log/mysql 2>/dev/null || true
    if [ ! -d /var/lib/mysql/mysql ]; then
        mysql_install_db --user=mysql --datadir=/var/lib/mysql >/dev/null
    fi
    nohup mysqld_safe --user=mysql --datadir=/var/lib/mysql >> /var/log/mariadb_boot.log 2>&1 &
    # wait until ready
    for i in $(seq 1 30); do
        if mysqladmin ping >/dev/null 2>&1; then break; fi
        sleep 1
    done
fi

# 3. Ensure DB user + database exist
mysql <<'EOF' >/dev/null
CREATE DATABASE IF NOT EXISTS meireles_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'meireles'@'localhost' IDENTIFIED BY 'meireles_pass';
CREATE USER IF NOT EXISTS 'meireles'@'127.0.0.1' IDENTIFIED BY 'meireles_pass';
GRANT ALL PRIVILEGES ON meireles_connect.* TO 'meireles'@'localhost';
GRANT ALL PRIVILEGES ON meireles_connect.* TO 'meireles'@'127.0.0.1';
FLUSH PRIVILEGES;
EOF

# 4. Init schema + seed if tables are missing
export DB_USER=meireles DB_PASS=meireles_pass DB_NAME=meireles_connect
N=$(mysql -umeireles -pmeireles_pass meireles_connect -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='meireles_connect';" 2>/dev/null || echo 0)
if [ "$N" -lt 5 ]; then
    echo "Seeding database..."
    php /app/php_app/config/init_db.php --cli
    php /app/php_app/seed_demo.php || true
fi

echo "==== bootstrap done — launching PHP server ===="

# 5. Exec PHP built-in web server (replaces current process so supervisor manages it)
exec php -d display_errors=1 -d error_reporting=E_ALL -S 0.0.0.0:3000 -t /app/php_app /app/php_app/router.php
