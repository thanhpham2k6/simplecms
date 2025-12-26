#!/bin/bash

#############################################
# SimpleCMS Auto Installer
# Ubuntu 20.04/22.04 LTS
#############################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
INSTALL_DIR="/var/www/html/simplecms"
DB_NAME="simplecms"
DB_USER="simplecms_user"
DOMAIN="localhost"

echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   SimpleCMS Auto Installer v1.0.0     ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Please run as root (use sudo)${NC}"
  exit 1
fi

# Get user inputs
read -p "Enter domain name [localhost]: " DOMAIN_INPUT
DOMAIN=${DOMAIN_INPUT:-$DOMAIN}

read -p "Enter database name [simplecms]: " DB_INPUT
DB_NAME=${DB_INPUT:-$DB_NAME}

read -p "Enter database user [simplecms_user]: " USER_INPUT
DB_USER=${USER_INPUT:-$DB_USER}

read -sp "Enter database password: " DB_PASS
echo ""

read -sp "Confirm database password: " DB_PASS_CONFIRM
echo ""

if [ "$DB_PASS" != "$DB_PASS_CONFIRM" ]; then
  echo -e "${RED}Passwords do not match!${NC}"
  exit 1
fi

echo ""
echo -e "${YELLOW}Starting installation...${NC}"
echo ""

# Update system
echo -e "${GREEN}[1/8] Updating system...${NC}"
apt update -qq

# Install Apache
if ! command -v apache2 &>/dev/null; then
  echo -e "${GREEN}[2/8] Installing Apache...${NC}"
  apt install -y apache2 >/dev/null
else
  echo -e "${GREEN}[2/8] Apache already installed${NC}"
fi

# Install MySQL
if ! command -v mysql &>/dev/null; then
  echo -e "${GREEN}[3/8] Installing MySQL...${NC}"
  apt install -y mysql-server >/dev/null
else
  echo -e "${GREEN}[3/8] MySQL already installed${NC}"
fi

# Install PHP
if ! command -v php &>/dev/null; then
  echo -e "${GREEN}[4/8] Installing PHP and extensions...${NC}"
  apt install -y php php-mysql php-gd php-mbstring php-xml php-curl libapache2-mod-php >/dev/null
else
  echo -e "${GREEN}[4/8] PHP already installed${NC}"
fi

# Create database
echo -e "${GREEN}[5/8] Creating database...${NC}"
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';" 2>/dev/null || true
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';" 2>/dev/null || true
mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Copy files
echo -e "${GREEN}[6/8] Setting up SimpleCMS files...${NC}"
CURRENT_DIR=$(pwd)

if [ "$CURRENT_DIR" != "$INSTALL_DIR" ]; then
  mkdir -p $INSTALL_DIR
  cp -r * $INSTALL_DIR/
fi

cd $INSTALL_DIR

# Create config file
echo -e "${GREEN}[7/8] Creating configuration file...${NC}"
if [ ! -f "config.php" ]; then
  cp config.sample.php config.php

  # Replace placeholders
  sed -i "s/DB_NAME', '.*'/DB_NAME', '$DB_NAME'/" config.php
  sed -i "s/DB_USER', '.*'/DB_USER', '$DB_USER'/" config.php
  sed -i "s/DB_PASS', '.*'/DB_PASS', '$DB_PASS'/" config.php
  sed -i "s|SITE_URL', '.*'|SITE_URL', 'http://$DOMAIN'|" config.php

  # Generate random keys
  AUTH_KEY=$(openssl rand -base64 32)
  SECURE_KEY=$(openssl rand -base64 32)
  sed -i "s/AUTH_KEY', '.*'/AUTH_KEY', '$AUTH_KEY'/" config.php
  sed -i "s/SECURE_AUTH_KEY', '.*'/SECURE_AUTH_KEY', '$SECURE_KEY'/" config.php
fi

# Set permissions
echo -e "${GREEN}[8/8] Setting permissions...${NC}"
chown -R www-data:www-data $INSTALL_DIR
chmod -R 755 $INSTALL_DIR
chmod -R 775 $INSTALL_DIR/uploads
chmod -R 775 $INSTALL_DIR/plugins
chmod -R 775 $INSTALL_DIR/themes

# Create necessary directories
mkdir -p $INSTALL_DIR/uploads
mkdir -p $INSTALL_DIR/cache
touch $INSTALL_DIR/uploads/.gitkeep

# Configure Apache
echo -e "${GREEN}Configuring Apache...${NC}"
cat >/etc/apache2/sites-available/simplecms.conf <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAdmin webmaster@$DOMAIN
    DocumentRoot $INSTALL_DIR
    
    <Directory $INSTALL_DIR>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/simplecms_error.log
    CustomLog \${APACHE_LOG_DIR}/simplecms_access.log combined
</VirtualHost>
EOF

a2ensite simplecms.conf >/dev/null 2>&1
a2enmod rewrite >/dev/null 2>&1
systemctl restart apache2

# Update PHP settings
echo -e "${GREEN}Optimizing PHP configuration...${NC}"
PHP_INI=$(php -i | grep "Loaded Configuration File" | awk '{print $5}')
if [ -f "$PHP_INI" ]; then
  sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' $PHP_INI
  sed -i 's/post_max_size = .*/post_max_size = 64M/' $PHP_INI
  sed -i 's/max_execution_time = .*/max_execution_time = 300/' $PHP_INI
  sed -i 's/memory_limit = .*/memory_limit = 256M/' $PHP_INI
  systemctl restart apache2
fi

# Create backup script
cat >$INSTALL_DIR/scripts/backup.sh <<'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/simplecms"
SITE_DIR="/var/www/html/simplecms"
DB_NAME="simplecms"
DB_USER="simplecms_user"
DB_PASS="REPLACE_WITH_PASSWORD"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql
tar -czf $BACKUP_DIR/files_$DATE.tar.gz $SITE_DIR
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
EOF

sed -i "s/REPLACE_WITH_PASSWORD/$DB_PASS/" $INSTALL_DIR/scripts/backup.sh
chmod +x $INSTALL_DIR/scripts/backup.sh

echo ""
echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Installation completed successfully! ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "1. Visit: ${GREEN}http://$DOMAIN/install.php${NC}"
echo -e "2. Complete the web installation"
echo -e "3. Login to admin panel: ${GREEN}http://$DOMAIN/admin${NC}"
echo ""
echo -e "${YELLOW}Database Info:${NC}"
echo -e "Database: ${GREEN}$DB_NAME${NC}"
echo -e "User: ${GREEN}$DB_USER${NC}"
echo -e "Password: ${GREEN}[hidden]${NC}"
echo ""
echo -e "${YELLOW}Backup:${NC}"
echo -e "Manual backup: ${GREEN}sudo $INSTALL_DIR/scripts/backup.sh${NC}"
echo -e "Setup auto backup: ${GREEN}crontab -e${NC}"
echo -e "Add line: ${GREEN}0 2 * * * $INSTALL_DIR/scripts/backup.sh${NC}"
echo ""
echo -e "${GREEN}Thank you for using SimpleCMS! 🚀${NC}"
