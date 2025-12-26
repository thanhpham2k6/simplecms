#!/bin/bash

#############################################
# SimpleCMS Update Script
# Cập nhật SimpleCMS từ Git repository
#############################################

# Configuration
INSTALL_DIR="/var/www/html/simplecms"
BACKUP_DIR="/var/backups/simplecms"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}SimpleCMS Update Script${NC}"
echo "================================"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo -e "${RED}Please run as root (use sudo)${NC}"
  exit 1
fi

# Backup before update
echo -e "${YELLOW}Creating backup before update...${NC}"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup files
tar -czf $BACKUP_DIR/pre_update_$DATE.tar.gz $INSTALL_DIR

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Backup completed${NC}"
else
  echo -e "${RED}✗ Backup failed${NC}"
  exit 1
fi

# Navigate to install directory
cd $INSTALL_DIR

# Check if git repository
if [ ! -d ".git" ]; then
  echo -e "${RED}This is not a git repository${NC}"
  exit 1
fi

# Stash local changes
echo "Stashing local changes..."
git stash

# Pull latest changes
echo "Pulling latest changes..."
git pull origin main

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Update completed${NC}"
else
  echo -e "${RED}✗ Update failed${NC}"
  git stash pop
  exit 1
fi

# Restore permissions
echo "Restoring permissions..."
chown -R www-data:www-data $INSTALL_DIR
chmod -R 755 $INSTALL_DIR
chmod -R 775 $INSTALL_DIR/uploads

# Restart Apache
echo "Restarting Apache..."
systemctl restart apache2

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}Update completed successfully!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "Backup location: $BACKUP_DIR/pre_update_$DATE.tar.gz"
echo ""

# Show git log
echo "Recent changes:"
git log --oneline -5
