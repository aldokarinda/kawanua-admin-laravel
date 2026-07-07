# Kawanua Admin Laravel

**Kawanua Admin Laravel** is a premium, modern, and lightweight admin dashboard template built on top of Laravel 13, Tailwind CSS, and Alpine.js. It is designed to be "Better Than Mazer", offering a cleaner SaaS-like aesthetic, a dynamic menu builder, and a robust Role-Based Access Control (RBAC) system right out of the box.

![Kawanua Admin Preview](https://via.placeholder.com/1200x600.png?text=Kawanua+Admin+Laravel+-+Premium+SaaS+Dashboard)

## ✨ Key Features

- **Built with Tailwind CSS & Alpine.js**: Lightning fast, zero jQuery, and no bulky CSS frameworks.
- **Dynamic Menu Builder**: Easily create, manage, and arrange your sidebar menus directly from the database with a visual tree hierarchy builder.
- **Advanced RBAC System**: Comprehensive User, Role, and Permission management using Laravel's native features.
- **Premium Split-Pane Forms**: Enterprise-grade UI/UX for form layouts, separating context from inputs for better readability.
- **Ultimate DataTables**: Clean, beautiful tables with bulk actions, search, filters, and premium icon actions.
- **Visual Icon Picker**: Pick Bootstrap Icons seamlessly when creating menus instead of pasting SVG codes.
- **Dark Mode Support**: Native dark mode implementation across the entire dashboard.

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL / PostgreSQL / SQLite

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/aldokarinda/kawanua-admin-laravel.git
   cd kawanua-admin-laravel
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```

5. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

6. **Configure your database in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kawanua
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run database migrations and seeders:**
   This will install the required tables and seed the Super Admin user, default roles, and menus.
   ```bash
   php artisan migrate --seed
   ```

8. **Compile front-end assets:**
   ```bash
   npm run build
   # Or for development: npm run dev
   ```

9. **Serve the application:**
   ```bash
   php artisan serve
   ```

## 🔑 Default Credentials

After running the seeder, you can log in with the default Super Admin account:

- **Email:** `admin@admin.com`
- **Password:** `password`

## 🛠️ Built With

- [Laravel 13](https://laravel.com/) - The PHP Framework for Web Artisans
- [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework
- [Alpine.js](https://alpinejs.dev/) - A rugged, minimal framework for composing JavaScript behavior in your markup
- [Bootstrap Icons](https://icons.getbootstrap.com/) - Free, high quality, open source icon library

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
