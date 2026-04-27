# University Finance Tracker System (UFTS) 🚀

A strict MVC web application built on the LAMP stack (Linux, Apache, MySQL/MariaDB, PHP) designed to help university students and faculty track and manage their finances.

**Recently upgraded to Phase 2**, transforming a core PHP framework into a polished, modern, highly interactive Progressive Web App (PWA) without relying on heavy JavaScript frameworks like React or Node.js.

## ✨ Features

### Phase 1: Core Foundation

- **Secure Authentication:** Registration and Login with bcrypt password hashing and RBAC (`student`, `admin`, `finance_officer`).
- **Transaction Management:** Full CRUD (Create, Read, Update, Delete) operations for incomes and expenses.
- **Analytical Reports & Budgets:** Track monthly spending limits and visually analyze data using Chart.js.
- **Admin & Finance Panels:** Manage users, system-wide data, and export records to CSV.

### Phase 2: Modernization & UX Upgrades (Recent Additions)

- **🎨 Complete Visual Redesign:** Fixed dark-mode sidebar layout with Bootstrap Icons, smooth `fade-in` transitions, and interactive hover effects. Preference saved in `localStorage`.
- **📊 Dynamic Dashboard:** 4 summary cards featuring animated Count-Up numbers, real-time progress bars for budget health, and interactive activity feeds.
- **🔍 Advanced Data Tables:** Filter (by category, type, date range), live search without reload, sortable column headers, pagination, and bulk row deletion.
- **📱 Progressive Web App (PWA):** Fully installable on mobile/desktop via `manifest.json`. Offline caching powered by a custom Service Worker (`sw.js`).
- **⚡ Performance & UX Polish:** AJAX-powered inline budget editing, loading skeleton screens, toast notifications, keyboard shortcuts (`N` for new, `D` for dashboard), and JS Polling for real-time notification bells.
- **🧠 Smart Insights Engine:** Pure PHP-generated personalized financial insights, spending pattern detection, and streak tracking.
- **🔐 Security Enhancements:** Profile avatars, preferred currency settings (e.g., UGX, GBP, USD), secure password updates, rate-limited login attempts (lockout after 5 fails), and persistent "Remember Me" cookies.

## 🛠️ Tech Stack & Architecture

- **Backend:** PHP 8.4 (Custom MVC Architecture, PRG Pattern)
- **Database:** MariaDB / MySQL (Optimized relational schema)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (ES6+), Bootstrap 5.3 (via CDN)
- **Data Visualization:** Chart.js (via CDN)
- **Zero Build Tools:** No npm, Node.js, Webpack, or frontend build steps required. Everything runs natively on a standard LAMP stack.

## 📁 Project Structure

```text
/
├── config/          # Database connection settings
├── controllers/     # Application logic and routing handlers
├── database/        # schema.sql (Run this to set up tables)
├── models/          # Data and database interaction layer
├── public/          # Static assets (CSS, JS, avatars, icons)
├── views/           # HTML templates, modals, and UI components
├── index.php        # Main application entry point and MVC router
├── manifest.json    # PWA Web Manifest
├── sw.js            # PWA Service Worker for offline caching
└── README.md        # Project documentation
```

## ⚙️ How to Run the Project Locally

Follow these steps to get the finance-tracker app running on your local machine.

### 1. Clone the repository

```bash
git clone git@github.com:SsemuliJoseph/University-Financial-Tracker-App.git
cd University-Financial-Tracker-App
```

_(Or navigate to your existing project folder e.g., `/var/www/html/finance-tracker`)_

### 2. Configure the Database

**For Windows (XAMPP/WAMP):**

1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Click **Shell** in XAMPP to open a terminal, then log into MySQL: `mysql -u root`

**For Linux/Mac:**

1. Log into your MariaDB/MySQL console: `sudo mysql -u root -p`

**Create the database and user (Run inside MySQL console):**

```sql
CREATE DATABASE IF NOT EXISTS finance_tracker;
GRANT ALL PRIVILEGES ON finance_tracker.* TO 'finance_user'@'localhost' IDENTIFIED BY 'finance_pass';
FLUSH PRIVILEGES;
EXIT;
```

**Import the database schema:**

- _Linux/Mac / Windows Shell:_ `mysql -u finance_user -pfinance_pass finance_tracker < database.sql`
- _Alternative:_ Open phpMyAdmin and import the `database.sql` file manually.

### 3. Start the Web Server

**Option A: Using PHP Built-in Server (Quickest method)**
Open a terminal in the project root directory and run:

```bash
php -S localhost:8000
```

Then open your browser and go to: `http://localhost:8000`

**Option B: Using Apache/Nginx (XAMPP/LAMP)**

- **Windows:** Ensure the project folder is inside your XAMPP `htdocs` folder (e.g., `C:\xampp\htdocs\finance-tracker`).
- **Linux/Mac:** Ensure the project is in your web root (e.g., `/var/www/html/finance-tracker`).
- Ensure the `mysqli` or `pdo_mysql` extension is enabled in your `php.ini`.
- Navigate to your local server in your web browser (e.g., `http://localhost/finance-tracker/`).

## 🤝 How to Contribute

We welcome contributions! Please follow this workflow to ensure code quality and prevent merge conflicts:

1. **Sync your fork:** Always pull the latest main branch before starting work:
   ```bash
   git checkout main
   git pull origin main
   ```
2. **Create a feature branch:** Name it clearly based on what you are building.
   ```bash
   git checkout -b feature/your-feature-name
   # or for bugfixes: git checkout -b fix/issue-name
   ```
3. **Follow the Architecture:**
   - Keep SQL queries strictly inside the `models/` directory.
   - Keep business logic and POST handling in the `controllers/`.
   - Keep pure HTML/UI inside `views/`. Do not echo HTML from controllers.
4. **Commit your changes:** Write clear, descriptive commit messages.
   ```bash
   git commit -m "feat: implement inline editing for budgets"
   ```
5. **Push and PR:** Push to your branch and open a Pull Request on GitHub for review.
   ```bash
   git push origin feature/your-feature-name
   ```

## 📝 License

This project is for educational purposes.
