#!/bin/bash

#############################################
# SimpleCMS Backup Script
# Tự động backup database và files
#############################################

# Configuration
BACKUP_DIR="/var/backups/simplecms"
SITE_DIR="/var/www/html/simplecms"
DB_NAME="simplecms"
DB_USER="simplecms_user"
DB_PASS="your_password_here"
DATE=$(date +%Y%m%d_%H%M%S)

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}SimpleCMS Backup Script${NC}"
echo "================================"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
echo "Backing up database..."
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME >$BACKUP_DIR/db_$DATE.sql

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Database backup completed${NC}"
else
  echo -e "${RED}✗ Database backup failed${NC}"
  exit 1
fi

# Backup files
echo "Backing up files..."
tar -czf $BACKUP_DIR/files_$DATE.tar.gz $SITE_DIR \
  --exclude='*.log' \
  --exclude='cache/*' \
  --exclude='tmp/*'

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Files backup completed${NC}"
else
  echo -e "${RED}✗ Files backup failed${NC}"
  exit 1
fi

# Delete old backups (older than 7 days)
echo "Cleaning old backups..."
find $BACKUP_DIR -name "db_*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +7 -delete

echo -e "${GREEN}✓ Old backups cleaned${NC}"

# Show backup info
DB_SIZE=$(du -h $BACKUP_DIR/db_$DATE.sql | cut -f1)
FILES_SIZE=$(du -h $BACKUP_DIR/files_$DATE.tar.gz | cut -f1)

echo ""
echo "Backup Summary:"
echo "--------------------------------"
echo "Date: $(date)"
echo "Database backup: $DB_SIZE"
echo "Files backup: $FILES_SIZE"
echo "Location: $BACKUP_DIR"
echo ""
echo -e "${GREEN}Backup completed successfully!${NC}"
