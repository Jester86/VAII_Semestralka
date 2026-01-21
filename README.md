# Forum Web Application

A Laravel-based forum application with features including user authentication, categories, topics, posts, private messaging, global chat, and admin panel.

## Features

- **User Authentication** - Register, login, logout
- **Forum Categories** - Browse and search categories
- **Topics & Posts** - Create topics, reply with posts, edit/delete
- **Private Messaging** - Send messages to other users with attachments
- **Global Chat** - Real-time chat widget
- **User Profiles** - View profiles, reputation voting system
- **Admin Panel** - User management, category management, password resets
- **Search** - Search across topics, posts, and categories

## Requirements

- PHP >= 8.0.2
- Composer
- Node.js & npm
- MySQL 8.0
- Docker & Docker Compose

---

## Installation with Docker

### 1. Clone the repository

```bash
git clone <repository-url>
cd projekt
```

### 2. Start Docker containers

```bash
docker-compose up -d
```

This will start:
- Laravel application on `http://localhost:8080`
- MySQL database on port `3306`

### 3. Install dependencies (inside container)

```bash
docker exec -it laravel-app composer install
docker exec -it laravel-app npm install
```

### 4. Setup environment

```bash
docker exec -it laravel-app cp .env.example .env
docker exec -it laravel-app php artisan key:generate
```

Edit `.env` file with these database settings:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 5. Run migrations

```bash
docker exec -it laravel-app php artisan migrate
```

### 6. Create storage link

```bash
docker exec -it laravel-app php artisan storage:link
```

### 7. Build frontend assets

```bash
docker exec -it laravel-app npm run build
```

### 8. Access the application

Open `http://localhost:8080` in your browser.

---

## Creating an Admin User

After running migrations, register a new user through the web interface, then promote them to admin via tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@example.com')->first();
$user->role = 'admin';
$user->save();
```

Or update directly in the database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
```

---

## Project Structure

```
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/              # Eloquent models
│   └── Helpers/             # Helper classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
├── public/                  # Public assets
└── storage/                 # File uploads, logs
```

---

