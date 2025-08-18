# Exam Attendance System

A web-based exam attendance management system built with PHP and MySQL.

## Prerequisites

- [Lando](https://docs.lando.dev/install/) - Download and install from https://docs.lando.dev/install/windows.html
- Git
- [Docker](https://docs.docker.com/desktop/setup/install/windows-install/) - Download and install from https://docs.docker.com/desktop/setup/install/windows-install/

## Local Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Project_updated
   ```

2. **Start the development environment**
   ```bash
   lando start
   ```

3. **Import the database**
   ```bash
   lando db-import exam_attendance_system.sql
   ```

4. **Create an admin user if you don't have it already**
   ```bash
   lando mysql -e "USE lamp; INSERT INTO users (name, email, password_hash, role) VALUES ('Admin', 'admin@test.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');"
   ```

## Access the Application

- **Main Application**: https://exam-attendance-system.lndo.site/
- **phpMyAdmin**: https://pma.exam-attendance.lndo.site/

## Default Login Credentials

- **Email**: admin@test.com
- **Password**: password

## Existing Users (from database)

- **Admin**: grp@gmail.com
- **Lecturer**: adasa1@gmail.com  
- **Students**: ahmed1@gmail.com, kingfahd@gmail.com

## Useful Commands

```bash
# Stop the environment
lando stop

# Restart the environment
lando restart

# Access MySQL CLI
lando mysql

# View application info
lando info

# Destroy environment (removes containers)
lando destroy
```

## Project Structure

```
exam_attendance_system/
├── assets/          # Images and static files
├── uploads/         # User uploaded files
├── *.php           # Application files
└── db.php          # Database configuration
```

## Troubleshooting

- If you get database connection errors, run `lando restart`
- Check logs with `lando logs`
- Ensure all containers are running with `lando info`
