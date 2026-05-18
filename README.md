# VENTO-corp
INTE302 - FINAL PROJECT

---

## 🛠️ Automated Database Setup & Seeding

This repository features **zero-setup automated database provisioning**. You do not need to manually create the database or import SQL files in phpMyAdmin!

### How It Works
1. **First Connection**: When you access any page in the application (like `public/auth.php`), the database layer automatically connects to MySQL.
2. **Auto Database Creation**: If the database `vento` does not exist, it is created automatically.
3. **Auto Schema Upload**: The system checks if the tables are already present. If the database is empty, it automatically reads [schema.sql](file:///c:/xampp/htdocs/VENTO-corp/database/schema.sql) and provisions all tables, constraints, and initial seed records (including administrator accounts, employee roles, and inventory states).

---

## 🔄 How to Reset / Sync Updated Schemas

If you or another team member updates the database structure in [schema.sql](file:///c:/xampp/htdocs/VENTO-corp/database/schema.sql), your local database won't auto-update by default (to prevent wiping out any testing data you've manually created). 

To synchronize your local database with the updated `schema.sql`, use the built-in **Developer Reset Trigger**:

1. Append `?db_reset=1` or `?db_reset=true` to any URL in your browser:
   * **Example**: `http://localhost/VENTO-corp/public/auth.php?db_reset=1`
2. **Under the hood**: 
   * The connection script will instantly drop your local `vento` database.
   * It recreates the database and re-runs the entire `schema.sql` file to give you a fresh, fully updated environment.
   * **Clean Redirection**: Once complete, it automatically redirects you back to the clean URL (e.g. `http://localhost/VENTO-corp/public/auth.php`) so that future page refreshes do not trigger another reset.

---

## ⚙️ Database Configuration
Database connection variables (host, username, password, database name) can be configured inside:
* **[config/db.php](file:///c:/xampp/htdocs/VENTO-corp/config/db.php)**

