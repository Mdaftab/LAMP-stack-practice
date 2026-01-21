# =============================================================================
# LAMP Stack Dockerfile - Single Container Setup
# =============================================================================
# This Dockerfile creates a single Linux container with Apache, MySQL, and PHP
# installed together - simulating a traditional LAMP server setup.
#
# For learning purposes: This mimics what you would do on a physical/virtual
# Linux server where all services run on the same machine.
# =============================================================================

# -----------------------------------------------------------------------------
# Step 1: Start with Ubuntu as our Linux base
# -----------------------------------------------------------------------------
# We use Ubuntu 22.04 LTS (Long Term Support) as it's stable and well-documented
FROM ubuntu:22.04

# Prevent interactive prompts during package installation
# This is important for automated Docker builds
ENV DEBIAN_FRONTEND=noninteractive

# -----------------------------------------------------------------------------
# Step 2: Update package lists and install essential packages
# -----------------------------------------------------------------------------
# Always update apt before installing packages to get latest versions
RUN apt-get update && apt-get install -y \
    # Supervisor - process manager to run multiple services in one container
    supervisor \
    # Curl - useful for testing and downloading
    curl \
    # Vim - text editor for debugging inside container
    vim \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# Step 3: Install Apache Web Server
# -----------------------------------------------------------------------------
# Apache2 is the "A" in LAMP - it serves web pages to browsers
RUN apt-get update && apt-get install -y \
    apache2 \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules needed for our setup
# - rewrite: allows URL rewriting (common for PHP frameworks)
RUN a2enmod rewrite

# -----------------------------------------------------------------------------
# Step 4: Install PHP and Required Extensions
# -----------------------------------------------------------------------------
# PHP is the "P" in LAMP - it processes dynamic web pages
RUN apt-get update && apt-get install -y \
    # PHP core and Apache module
    php \
    libapache2-mod-php \
    # MySQL extension - allows PHP to connect to MySQL
    php-mysql \
    # Common PHP extensions
    php-cli \
    php-common \
    php-json \
    php-mbstring \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# Step 5: Install MySQL Server
# -----------------------------------------------------------------------------
# MySQL is the "M" in LAMP - it stores and manages data
RUN apt-get update && apt-get install -y \
    mysql-server \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# Step 6: Configure MySQL
# -----------------------------------------------------------------------------
# Create directory for MySQL socket and set permissions
RUN mkdir -p /var/run/mysqld && \
    chown mysql:mysql /var/run/mysqld && \
    chmod 755 /var/run/mysqld

# Configure MySQL to accept connections from PHP
# By default, MySQL only allows local connections which is what we want
RUN sed -i 's/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf || true

# -----------------------------------------------------------------------------
# Step 7: Configure Apache
# -----------------------------------------------------------------------------
# Set ServerName to suppress warning messages
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy our custom Apache virtual host configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Set proper permissions for web directory
RUN chown -R www-data:www-data /var/www/html

# -----------------------------------------------------------------------------
# Step 8: Copy Supervisor Configuration
# -----------------------------------------------------------------------------
# Supervisor manages multiple processes (Apache + MySQL) in this single container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# -----------------------------------------------------------------------------
# Step 9: Create directory for initialization scripts
# -----------------------------------------------------------------------------
# The SQL init script will be mounted via docker-compose volume
RUN mkdir -p /docker-entrypoint-initdb.d

# -----------------------------------------------------------------------------
# Step 10: Create Startup Script
# -----------------------------------------------------------------------------
# This script initializes MySQL and starts Supervisor
RUN echo '#!/bin/bash\n\
# Initialize MySQL data directory if empty\n\
if [ ! -d "/var/lib/mysql/mysql" ]; then\n\
    echo "Initializing MySQL data directory..."\n\
    mysqld --initialize-insecure --user=mysql\n\
fi\n\
\n\
# Start MySQL temporarily to run initialization\n\
echo "Starting MySQL for initialization..."\n\
mysqld --user=mysql &\n\
MYSQL_PID=$!\n\
\n\
# Wait for MySQL to be ready\n\
echo "Waiting for MySQL to be ready..."\n\
for i in {1..30}; do\n\
    if mysqladmin ping -h localhost --silent 2>/dev/null; then\n\
        break\n\
    fi\n\
    sleep 1\n\
done\n\
\n\
# Set root password and create database\n\
echo "Configuring MySQL..."\n\
mysql -u root <<EOF\n\
ALTER USER "root"@"localhost" IDENTIFIED WITH mysql_native_password BY "rootpassword";\n\
CREATE USER IF NOT EXISTS "root"@"%" IDENTIFIED WITH mysql_native_password BY "rootpassword";\n\
GRANT ALL PRIVILEGES ON *.* TO "root"@"%" WITH GRANT OPTION;\n\
FLUSH PRIVILEGES;\n\
EOF\n\
\n\
# Run initialization SQL if it exists and database does not exist\n\
if [ -f "/docker-entrypoint-initdb.d/init.sql" ]; then\n\
    echo "Running database initialization script..."\n\
    mysql -u root -prootpassword < /docker-entrypoint-initdb.d/init.sql\n\
fi\n\
\n\
# Stop the temporary MySQL instance\n\
echo "Stopping temporary MySQL instance..."\n\
kill $MYSQL_PID 2>/dev/null\n\
wait $MYSQL_PID 2>/dev/null\n\
\n\
# Start Supervisor (which will manage both Apache and MySQL)\n\
echo "Starting Supervisor..."\n\
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf\n\
' > /start.sh && chmod +x /start.sh

# -----------------------------------------------------------------------------
# Step 11: Expose Ports
# -----------------------------------------------------------------------------
# Port 80: Apache web server (HTTP)
# Port 3306: MySQL (for external database tools if needed)
EXPOSE 80 3306

# -----------------------------------------------------------------------------
# Step 12: Set Working Directory
# -----------------------------------------------------------------------------
WORKDIR /var/www/html

# -----------------------------------------------------------------------------
# Step 13: Start the Services
# -----------------------------------------------------------------------------
# Run our startup script which initializes MySQL and starts Supervisor
CMD ["/start.sh"]
