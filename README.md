# LAMP Stack Demo

A complete LAMP (Linux, Apache, MySQL, PHP) stack running in a single Docker container for learning and demonstration purposes.

## What is LAMP?

LAMP is one of the most popular web development stacks:

| Component | Description | Our Version |
|-----------|-------------|-------------|
| **L**inux | Operating System | Ubuntu 22.04 |
| **A**pache | Web Server | Apache 2.4 |
| **M**ySQL | Database | MySQL 8.0 |
| **P**HP | Programming Language | PHP 8.1 |

## Architecture

This project runs all LAMP components in a **single Docker container**, simulating a traditional server setup where everything runs on one machine.

```
┌─────────────────────────────────────────────────────────────┐
│                    Docker Container                          │
│  ┌─────────────────────────────────────────────────────┐    │
│  │                 Ubuntu 22.04                         │    │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  │    │
│  │  │   Apache    │  │    PHP      │  │   MySQL     │  │    │
│  │  │  (Port 80)  │◄─┤  (mod_php)  │◄─┤ (Port 3306) │  │    │
│  │  └─────────────┘  └─────────────┘  └─────────────┘  │    │
│  │                                                      │    │
│  │  ┌─────────────────────────────────────────────┐    │    │
│  │  │  Supervisor (Process Manager)                │    │    │
│  │  │  - Manages Apache and MySQL processes        │    │    │
│  │  └─────────────────────────────────────────────┘    │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
        │                                       │
        ▼                                       ▼
   Host:8080                              mysql_data
   (Web Access)                           (Persistent Volume)
```

## Prerequisites

Before starting, ensure you have:

1. **Docker Desktop** (or Docker Engine)
   - [Download for Mac/Windows](https://www.docker.com/products/docker-desktop)
   - [Install on Linux](https://docs.docker.com/engine/install/)

2. **Task** (Task runner)
   ```bash
   # macOS
   brew install go-task
   
   # Linux
   sh -c "$(curl -fsSL https://taskfile.dev/install.sh)" -- -d -b /usr/local/bin
   
   # Windows
   choco install go-task
   ```

## Quick Start

1. **Clone or download this project**

2. **Start the LAMP stack**
   ```bash
   task up
   ```

3. **Open your browser**
   - Visit: http://localhost:8080

4. **When finished, stop the stack**
   ```bash
   task down
   ```

## Available Commands

| Command | Description |
|---------|-------------|
| `task up` | Build and start the LAMP stack |
| `task down` | Stop containers (preserves data) |
| `task logs` | View container logs |
| `task status` | Show container status |
| `task shell` | Open bash shell in container |
| `task mysql` | Connect to MySQL CLI |
| `task rebuild` | Force rebuild Docker image |
| `task reset-db` | Reset database (deletes data!) |
| `task clean` | Remove everything |
| `task info` | Show project information |

## Project Structure

```
LAMP/
├── app/                    # PHP Application
│   ├── index.php          # Main application file
│   ├── config.php         # Database configuration
│   └── styles.css         # CSS styling
├── docker/                 # Docker Configuration
│   ├── Dockerfile         # Container build instructions
│   ├── apache.conf        # Apache virtual host config
│   └── supervisord.conf   # Process manager config
├── sql/
│   └── init.sql           # Database initialization
├── docker-compose.yml     # Container orchestration
├── Taskfile.yml           # Task automation
└── README.md              # This file
```

## How the Demo App Works

The demo application demonstrates full LAMP integration:

### Data Flow

```
┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│  Browser │────▶│  Apache  │────▶│   PHP    │────▶│  MySQL   │
│          │     │          │     │          │     │          │
│ Form     │     │ Receives │     │ Processes│     │ Stores/  │
│ Submit   │     │ HTTP     │     │ Request  │     │ Retrieves│
└──────────┘     └──────────┘     └──────────┘     └──────────┘
     ▲                                                   │
     │                                                   │
     └───────────────────────────────────────────────────┘
                    HTML Response
```

### Features Demonstrated

1. **INSERT Operation** - Adding new users via the form
2. **SELECT Operation** - Displaying all users in a table
3. **DELETE Operation** - Removing users from the database

### Key Files Explained

#### `app/config.php`
Establishes the connection between PHP and MySQL:
```php
$conn = mysqli_connect('localhost', 'root', 'rootpassword', 'lamp_demo');
```

#### `app/index.php`
The main application that:
- Handles form submissions (POST requests)
- Executes SQL queries (INSERT, SELECT, DELETE)
- Renders HTML with PHP

#### `sql/init.sql`
Creates the database and initial data:
```sql
CREATE DATABASE lamp_demo;
CREATE TABLE users (id, name, email, created_at);
INSERT INTO users VALUES (...);
```

## Configuration

### Database Credentials

| Setting | Value |
|---------|-------|
| Host | `localhost` (inside container) or `localhost:3307` (from host) |
| Username | `root` |
| Password | `rootpassword` |
| Database | `lamp_demo` |

### Ports

| Service | Container Port | Host Port |
|---------|---------------|-----------|
| Apache (HTTP) | 80 | 8080 |
| MySQL | 3306 | 3307 |

## Troubleshooting

### Container won't start
```bash
# Check logs for errors
task logs

# Rebuild from scratch
task clean
task up
```

### Database connection error
```bash
# Wait 30-60 seconds for MySQL to initialize
# Check if MySQL is running
task shell
mysql -u root -prootpassword -e "SELECT 1"
```

### Changes not appearing
```bash
# The app/ directory is mounted live, so PHP changes appear immediately
# For Dockerfile changes, rebuild:
task rebuild
task up
```

### Port already in use
```bash
# Check what's using port 8080
lsof -i :8080

# Or change the port in docker-compose.yml:
# ports:
#   - "8081:80"  # Use 8081 instead
```

### Reset everything
```bash
# Nuclear option - removes all data
task clean
task up
```

## Learning Resources

- [Docker Documentation](https://docs.docker.com/)
- [Apache HTTP Server](https://httpd.apache.org/docs/)
- [PHP Manual](https://www.php.net/manual/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Taskfile Documentation](https://taskfile.dev/)

## Why Single Container?

For production, you would typically run each service in a separate container. However, this single-container approach:

- **Simulates a traditional server** - Shows how LAMP worked before containers
- **Easier to understand** - All components visible in one place
- **Good for learning** - Focus on LAMP concepts, not container orchestration
- **Simple deployment** - One container to manage

## License

This project is for educational purposes. Feel free to use and modify as needed.
