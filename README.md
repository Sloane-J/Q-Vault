# Q-Vault - Past Exam Paper Management System

![Project Banner](https://via.placeholder.com/1200x400/3b82f6/ffffff?text=Past+Exam+Paper+Management)

**Q-Vault** is a robust Laravel + Livewire application designed for managing and accessing past examination papers with ease. The system supports both administrative and student access levels, offering secure upload, search, and analytics features.

---

## 🚀 Features

### Role-Based Access Control
- Dedicated admin dashboard with full control
- Student portal with search and download access

### Paper Management
- Upload, versioning, and secure storage
- Advanced filtering and keyword search
- Download tracking and usage logging

### Departmental Organization
- Papers categorized by department and academic level
- Supports HND and B-Tech distinctions

### Analytics Dashboard
- View download statistics and trends
- Monitor user activities and engagement

---

## 🛠️ Technology Stack

### Core
- **Laravel 10** – Backend framework
- **Livewire 3** – Reactive components without SPA complexity
- **Alpine.js** – Lightweight frontend interactivity
- **Tailwind CSS** – Utility-first CSS framework

### Database
- **MySQL** (Production)
- **SQLite** (Development)

### Key Packages
- **Laravel Snappy** – PDF generation
- **Spatie Permissions** – Role and permission management
- **Laravel Excel** – Import/export capabilities

---

## ⚙️ Installation

### Prerequisites
- PHP 8.2+
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+ or SQLite (for local dev)

### Setup Instructions

```bash
# Clone the repo
git clone https://github.com/Sloane-J/q-vault.git
cd past-exam-system

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Set up database (SQLite example)
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the server
php artisan serve

## Common Artisan and NPM Commands
# Run tests
php artisan test

# Start development servers
npm run dev

# Create Livewire component
php artisan make:livewire Student/PaperBrowser

# Clear all caches
php artisan optimize:clear

| Key              | Description             | Example |
| ---------------- | ----------------------- | ------- |
| APP\_ENV         | Application environment | local   |
| DB\_CONNECTION   | Database driver         | sqlite  |
| FILESYSTEM\_DISK | Disk for storing papers | local   |


Project Structure
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

🚢 Deployment
Production Setup
Configure .env with production database and storage values

Set up a queue worker for background tasks

Create a symbolic link for storage: php artisan storage:link

Deployment Script

git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan optimize

🤝 Contributing
Fork the repository

Create a feature branch:
git checkout -b feature/amazing-feature

COMMIT YOUR CHANGES
git commit -m "Add amazing feature"

PUSH TO GITHUB 
git push origin feature/amazing-feature

📄 License
This project is open-source and available under the MIT License.

🙋 Support
For issues or questions, please open an issue.

Maintainers
[Your Name] – [your.email@example.com]

[Team Member] – [team.member@example.com]