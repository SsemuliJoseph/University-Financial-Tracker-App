# University Finance Tracker System (UFTS)

A strict MVC web application built on the LAMP stack (Linux, Apache, MySQL/MariaDB, PHP) designed to help university students and faculty track and manage their finances.

## 🚀 Features

- **Secure Authentication:** Registration and Login with bcrypt password hashing.
- **Role-Based Access Control (RBAC):** Distinct roles for `student`, `admin`, and `finance_officer`.
- **Dashboard:** At-a-glance view of current balance, recent transactions, and spending progress.
- **Transaction Management:** Full CRUD (Create, Read, Update, Delete) operations for incomes and expenses.
- **Budget Tracking:** Set monthly limits and visually track spending progress.
- **Analytical Reports:** Interactive pie and line charts using Chart.js.
- **Admin Panel:** User management interface (change roles, delete accounts).
- **Finance Officer View:** Read-only system-wide transaction data with CSV export functionality.

## 🛠️ Tech Stack

- **Backend:** PHP 8.4
- **Database:** MariaDB / MySQL
- **Frontend:** HTML5, CSS3, Vanilla Javascript, Bootstrap 5 (CDN)
- **Architecture:** Custom MVC (Model-View-Controller)

## 📁 Project Structure

```text
/
├── config/          # Database connection settings
├── controllers/     # Application logic and routing handlers
├── database/        # Database schema files
├── models/          # Data and database interaction layer
├── public/          # Static assets (CSS, JS, Images)
├── views/           # HTML templates and UI components
├── index.php        # Main application entry point and router
└── README.md        # Project documentation
```

## ⚙️ Local Development Setup (For Collaborators)

### 1. Clone the repository

```bash
git clone git@github.com:SsemuliJoseph/University-Financial-Tracker-App.git
cd University-Financial-Tracker-App
```

### 2. Configure the Database

**For Windows (XAMPP/WAMP):**

1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Click **Shell** in XAMPP to open a terminal, then log into MySQL:

```bash
mysql -u root
```

**For Linux/Mac:**

1. Log into your MariaDB/MySQL console:

```bash
sudo mysql -u root -p
```

2. Create the database and user (Run this in the MySQL console for both OS):

```sql
CREATE DATABASE IF NOT EXISTS finance_tracker;
GRANT ALL PRIVILEGES ON finance_tracker.* TO 'finance_user'@'localhost' IDENTIFIED BY 'finance_pass';
FLUSH PRIVILEGES;
EXIT;
```

3. Import the database schema:

**For Windows (XAMPP):**
You can either import `database.sql` directly through **phpMyAdmin** (`http://localhost/phpmyadmin`), or via the XAMPP shell:

```bash
mysql -u finance_user -pfinance_pass finance_tracker < database.sql
```

**For Linux/Mac:**

```bash
mysql -u finance_user -pfinance_pass finance_tracker < database.sql
```

### 3. Server Configuration

**For Windows:**
Ensure you cloned the repository into your XAMPP `htdocs` folder (e.g., `C:\xampp\htdocs\University-Financial-Tracker-App`).

**For Linux/Mac:**
Ensure your local Apache or Nginx server points to the project root directory (e.g., `/var/www/html/University-Financial-Tracker-App`).

Navigate to your local server (e.g., `http://localhost/University-Financial-Tracker-App/` or `http://localhost/finance-tracker/`) in your web browser.

## 🤝 Collaborating

1. **Pull the latest changes** before starting work: `git pull origin main`
2. **Create a feature branch:** `git checkout -b feature/your-feature-name`
3. **Commit your changes:** `git commit -m "Add some feature"`
4. **Push to the branch:** `git push origin feature/your-feature-name`
5. **Open a Pull Request** on GitHub for review.

## 📝 License

This project is for educational purposes.
