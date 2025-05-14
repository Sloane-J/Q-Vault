# Q-Vault
# Past Exam Paper Management System

![Project Banner](https://via.placeholder.com/1200x400/3b82f6/ffffff?text=Past+Exam+Paper+Management)

A comprehensive digital platform for managing and accessing past exam papers, built with Laravel and Livewire.

## Features

- **Role-Based Access Control**
  - Admin dashboard with full system control
  - Student portal for paper access
- **Paper Management**
  - Upload and version control
  - Advanced search with filters
  - Download tracking
- **Department Organization**
  - Categorize papers by department
  - HND/B-Tech level differentiation
- **Analytics Dashboard**
  - Download statistics
  - User activity monitoring

## Technology Stack

### Core
- **Laravel 10** - PHP framework
- **Livewire 3** - Full-stack framework
- **Alpine.js** - Frontend interactivity
- **Tailwind CSS** - Utility-first styling

### Database
- MySQL (Production)
- SQLite (Development)

### Additional Packages
- Laravel Snappy (PDF generation)
- Spatie Permissions (Role management)
- Laravel Excel (Data export)

## Installation

### Prerequisites
- PHP 8.2+
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+ or SQLite

### Setup Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/past-exam-system.git
   cd past-exam-system
   ```
2. composer install
npm install

cp .env.example .env

php artisan key:generate

touch database/database.sqlite  # For SQLite development
php artisan migrate --seed

npm run build

php artisan serve

Common Commads

# Run tests
php artisan test

# Start development servers
npm run dev

# Generate Livewire component
php artisan make:livewire Student/PaperBrowser

# Clear caches
php artisan optimize:clear

Environment Variables
Key	Description	Example
APP_ENV	Application environment	local
DB_CONNECTION	Database driver	sqlite
FILESYSTEM_DISK	Paper storage	local


## Project Structure

app/
├── Http/
│   ├── Controllers/
│   └── Livewire/
│       ├── Admin/
│       └── Student/
config/
database/
├── migrations/
├── seeders/
public/
resources/
├── js/
├── css/
└── views/
routes/
tests/

## Deployment
Production Requirements
Configure .env with production values

Set up queue worker for background jobs

Configure storage link: php artisan storage:link

## Deployment Scripts
 git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan optimize

Contributing
Fork the repository

Create feature branch (git checkout -b feature/amazing-feature)

Commit changes (git commit -m 'Add amazing feature')

Push to branch (git push origin feature/amazing-feature)

Open Pull Request

License
MIT License. See LICENSE for more information.

Support
For issues or questions, please open an issue.

Project Maintainers:
[Your Name] - [your.email@example.com]
[Team Member] - [team.member@example.com]