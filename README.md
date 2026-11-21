# Lab 11: User Validation Web Application

A secure, production-grade PHP & Database user authentication web application. Users submit their **login name** and **password** to the server, which validates the credentials against pre-existing user records stored in a Database. Upon match, a **successful login page** is returned; otherwise, an explicit **failure message** is returned to the user.

---

## 📌 Problem Statement & Requirements

> **"A user validation web application, where the user submits the login name & password to the server. The name and password are checked against the data already available in Database and if the data matches, a successful login page is returned. Otherwise failure message is shown to the user"**

---

## 📁 Project Directory Structure

```text
lab 11/
├── index.php             # Application router (redirects to login.php or login_success.php)
├── config.php            # Database PDO connection factory (Supports SQLite & MySQL)
├── login.php             # User login interface with interactive quick-fill demo chips
├── validate.php          # Server-side authentication controller (PDO prepared statements)
├── login_success.php     # Successful login dashboard displaying user profile & session details
├── login_failure.php     # Explicit failure page displaying failure audit & error message
├── register.php          # User registration interface to insert new accounts into Database
├── logout.php            # Secure session termination controller
├── view_users.php        # Database records inspector (shows hashed credentials & roles)
├── setup_database.php    # Reset & auto-seed utility for SQLite & MySQL
├── schema.sql            # Standard SQL schema script for MySQL / phpMyAdmin import
├── style.css             # Responsive, modern UI stylesheet
├── users.db              # SQLite zero-configuration database (auto-generated & pre-seeded)
└── README.md             # Lab documentation, execution manual & viva questions
```

---

## 🏗️ Architecture & Authentication Flow

```text
+-------------------+             POST username & password
|                   | -----------------------------------------------------> +----------------------+
|     login.php     |                                                        |     validate.php     |
| (HTML Login Form) | <----------------------------------------------------- | (Server Controller)  |
+-------------------+            (If redirected or error parameter)          +----------+-----------+
                                                                                        |
                                                                           Queries Database with PDO
                                                                           Prepared Statement
                                                                                        |
                                                                                        v
                                                                             +----------------------+
                                                                             |     Database Table   |
                                                                             |       (`users`)      |
                                                                             +----------+-----------+
                                                                                        |
                                                 +--------------------------------------+--------------------------------------+
                                                 |                                                                             |
                                     Credentials Match!                                                          Credentials DO NOT Match
                                                 |                                                                             |
                                                 v                                                                             v
                                      +----------------------+                                                      +----------------------+
                                      |  login_success.php   |                                                      |  login_failure.php   |
                                      | (Protected Dashboard |                                                      | (Explicit Failure    |
                                      |  Welcome User Page)  |                                                      |  Message Shown)      |
                                      +----------------------+                                                      +----------------------+
```

### Detailed Execution Flow:
1. **User Submission (`login.php`)**:
   - The user enters their **Login Name / Username** and **Password** into an HTML form.
   - Form method is set to `POST` to ensure credentials are sent securely in the HTTP request body instead of visible in the URL.
2. **Server-Side Validation (`validate.php`)**:
   - Sanitizes and verifies that neither the username nor password is empty.
   - Connects to the Database using **PDO (PHP Data Objects)**.
   - Executes a parameterized prepared query:
     ```sql
     SELECT id, username, full_name, email, password_hash, role, created_at 
     FROM users 
     WHERE LOWER(username) = LOWER(:username) 
     LIMIT 1
     ```
   - Verifies the submitted password against the stored cryptographic hash using `password_verify($password, $user['password_hash'])`.
3. **Branch 1: Credentials Match**:
   - Calls `session_regenerate_id(true)` to mitigate Session Fixation attacks.
   - Stores user authentication state (`user_id`, `username`, `full_name`, `role`, `login_time`) in `$_SESSION`.
   - Redirects to **`login_success.php`**, presenting a welcome message, user profile details, and a logout button.
4. **Branch 2: Credentials Do Not Match**:
   - Redirects to **`login_failure.php`**, displaying the prompt-required failure message: *"The login name or password you entered does not match any record in our database."*
   - Displays a *"Try Again"* button to return to the login interface.

---

## ⚡ Pre-Seeded Demonstration Accounts

The database comes pre-seeded with 3 verified test accounts:

| Role | Login Name (Username) | Plaintext Password | Stored Bcrypt Hash Preview | Full Name |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `Admin@123` | `$2y$10$...` | System Administrator |
| **Student** | `student` | `Lab11@Pass` | `$2y$10$...` | John Doe |
| **Faculty** | `alice` | `Alice@2026` | `$2y$10$...` | Alice Johnson |

> **Pro-Tip**: On `login.php`, click any of the **Quick-Fill Demo Chips** at the bottom of the card to instantly populate the credentials and test successful authentication or intentional failure in 1 click!

---

## 🚀 How to Run the Application

### Option 1: Using PHP's Built-In Web Server (Recommended - Zero Setup!)
This requires **no MySQL setup or external software** because it utilizes PHP 8.3's built-in SQLite engine:

1. Open PowerShell or Terminal and navigate to this directory:
   ```powershell
   cd "c:\Users\Acer\OneDrive\文档\web programming\lab 11"
   ```
2. Start the PHP development server:
   ```powershell
   php -S localhost:8000
   ```
3. Open your web browser and navigate to:
   * **Login Page**: [http://localhost:8000/login.php](http://localhost:8000/login.php)
   * **Database Inspector**: [http://localhost:8000/view_users.php](http://localhost:8000/view_users.php)
   * **Account Registration**: [http://localhost:8000/register.php](http://localhost:8000/register.php)

---

### Option 2: Using MySQL / XAMPP / WampServer
If your evaluator specifically asks to demonstrate using a **MySQL database**:

1. **Import the Database Schema**:
   - Open **phpMyAdmin** or your MySQL command-line client.
   - Run or import the SQL script provided in [`schema.sql`](./schema.sql):
     ```bash
     mysql -u root -p < schema.sql
     ```
2. **Switch Driver in `config.php`**:
   - Open [`config.php`](./config.php) and change:
     ```php
     define('DB_TYPE', 'mysql'); // Changed from 'sqlite' to 'mysql'
     define('MYSQL_HOST', 'localhost');
     define('MYSQL_PORT', '3306');
     define('MYSQL_DB',   'lab11_auth');
     define('MYSQL_USER', 'root');
     define('MYSQL_PASS', ''); // Your MySQL root password
     ```
3. Copy the `lab 11` folder into your web root (e.g., `C:\xampp\htdocs\lab11`) or start the server via `php -S localhost:8000`.

---

## 🛡️ Security Features & Best Practices Implemented

1. **SQL Injection Prevention**:
   - Implemented exclusively using **PDO Parameterized Prepared Statements** (`:username` placeholder). User input is never concatenated directly into SQL queries.
2. **Password Cryptographic Hashing**:
   - Passwords are never stored as plain text. The application uses PHP's industry-standard `password_hash($pass, PASSWORD_BCRYPT)` and verifies with `password_verify()`.
3. **Session Fixation Defense**:
   - Invokes `session_regenerate_id(true)` upon successful authentication, replacing the existing session ID with a new one to invalidate any pre-authenticated session token.
4. **Cross-Site Scripting (XSS) Prevention**:
   - All server output rendered to the client is escaped through `htmlspecialchars()` with `ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'`.
5. **Protection Against User Enumeration**:
   - Failed logins return a uniform *"Invalid login name or password"* failure notice, preventing malicious actors from discerning whether a username exists in the system.

---

## 💡 Key Technical Concepts (Lab Viva & Exam Preparation)

### Q1: Why should we use `POST` instead of `GET` for login forms?
* **Answer**: `GET` appends form parameters directly to the URL query string (`?username=admin&password=123`), which exposes passwords in browser history, server access logs, and HTTP referer headers. `POST` transmits credentials securely within the HTTP request payload body.

### Q2: What is SQL Injection and how do Prepared Statements prevent it?
* **Answer**: SQL Injection happens when attacker-controlled input is concatenated directly into an SQL statement string, altering the query logic (e.g., `' OR '1'='1`). Prepared statements separate query structure from data parameters; the database compiles the query first and treats user parameters strictly as values, never as executable code.

### Q3: Why is storing plain-text passwords or simple MD5 hashes considered bad practice?
* **Answer**: In the event of a database breach, plain-text passwords immediately expose user credentials across all services they use. MD5 and SHA-1 are cryptographically broken, extremely fast to brute-force, and vulnerable to precomputed Rainbow Table attacks. Bcrypt introduces a configurable work factor (cost) and built-in random salting.

### Q4: What is the difference between Authentication and Authorization?
* **Answer**:
  * **Authentication (`AuthN`)**: Verifying *who* the user is (e.g., validating username and password against database records).
  * **Authorization (`AuthZ`)**: Determining *what* permissions or resources an authenticated user is permitted to access (e.g., Administrator vs Student role).

### Q5: How does `session_start()` maintain user state in PHP?
* **Answer**: HTTP is a stateless protocol. When `session_start()` is called, PHP generates a unique Session ID (`PHPSESSID`) transmitted to the browser as a cookie. On subsequent requests, the browser sends this cookie back, allowing PHP to look up the user's session data stored on the server in `$_SESSION`.
