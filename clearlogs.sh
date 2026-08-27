#!/bin/bash
# Delete files older than 7 days from logs and cache

LOGS_DIR="/data/www/www_accenttravel_ro/app/logs/travelfuse"
CACHE_DIR="/data/www/www_accenttravel_ro/app/cache/travelfuse"

# Remove files older than 7 days
find "$LOGS_DIR" -type f -mtime +1 -exec rm -f {} \;
find "$CACHE_DIR" -type f -mtime +1 -exec rm -f {} \;

# (Optional) Remove empty directories left behind
find "$LOGS_DIR" -type d -empty -delete
find "$CACHE_DIR" -type d -empty -delete


# LOGS_DIR2="/data/www/www_accenttravel_ro/app/logs/travelfuse"
CACHE_DIR2="/data/www/www_accenttravel_ro/app/cache/newux/trip"

# Remove files older than 7 days
# find "$LOGS_DIR2" -type f -mtime +1 -exec rm -f {} \;
find "$CACHE_DIR2" -type f -mtime +7 -exec rm -f {} \;

# (Optional) Remove empty directories left behind
# find "$LOGS_DIR2" -type d -empty -delete
find "$CACHE_DIR2" -type d -empty -delete

echo "done"