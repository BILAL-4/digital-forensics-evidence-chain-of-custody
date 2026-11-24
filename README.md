# Digital Forensics Evidence & Chain-of-Custody System

**Digital Forensics Evidence & Chain-of-Custody System** is a PHP and MySQL investigation platform for managing cyber-crime cases and preserving evidence integrity. It combines SHA-256 evidence verification, role-based access control, and hash-linked audit records to support defensible digital-forensics workflows.

## 

### 1. Investigation Dashboard


- **Description:** Displays case activity, complaint status, assigned staff, and investigation workload through a role-aware interface.


> **Screenshot placeholder:** Add a screenshot here showing the officer or forensic dashboard.


### 2. Evidence Chain-of-Custody Console


- **Description:** Records evidence type, file metadata, collecting officer, case association, custody narrative, and a validated 64-character SHA-256 digest.


> **Screenshot placeholder:** Add a screenshot here showing evidence logging and integrity metadata.


### 3. Role-Based Access Control


- **Description:** Separates citizen, officer, analyst, forensic-expert, and administrator capabilities, restricting sensitive actions to authorized roles.


### 4. Tamper-Evident Audit Trail


- **Description:** Hash-links each audit entry to the preceding entry so record modifications create a detectable break in the audit chain.


### 5. Case and Investigation Management


- **Description:** Tracks complaints, case lifecycle, assigned personnel, suspects, warrants, investigative records, and forensic reports.


## ✨ Core Features


- **SHA-256 Validation:** Requires a valid 64-character evidence hash before logging a record.
- **Chain-of-Custody Tracking:** Records collector identity, collection date, custody narrative, case, and evidence metadata.
- **Strict RBAC:** Controls access to citizen, analyst, investigator, forensic, and administrator operations.
- **Hash-Linked Audit Logs:** Uses `PreviousHash` and `EntryHash` to provide tamper-evident audit chaining.
- **Parameterized Database Queries:** Uses PDO prepared statements and bound parameters against SQL injection.
- **CSRF Protection:** Verifies mutation requests with session-bound CSRF tokens.
- **Session Resilience:** Regenerates the PHP session identifier after successful authentication.


## 📋 Prerequisites


Before setting up the system, make sure you have:

- **PHP:** 8.0 or higher
- **MySQL/MariaDB:** 8.0 or compatible
- **Web Server:** Apache or another PHP-enabled server
- **PHP Extensions:** PDO MySQL enabled
- **Environment:** XAMPP is recommended for local setup


## 🛠️ Installation & Setup


### Step 1: Clone the Repository


```bash
git clone https://github.com/BILAL-4/digital-forensics-evidence-chain-of-custody.git
cd digital-forensics-evidence-chain-of-custody
```


### Step 2: Import the Database


Start MySQL and import the demo schema:

```bash
mysql -u root -p < ccrims/database.sql
```


### Step 3: Configure Environment Variables


Set the variables shown in `.env.example`:

```text
CCRIMS_DB_HOST=localhost
CCRIMS_DB_NAME=ccrims
CCRIMS_DB_USER=root
CCRIMS_DB_PASS=
```


### Step 4: Deploy Locally


Place the project in the Apache web root. With XAMPP, this is usually `htdocs/`. Then open:

```text
http://localhost/digital-forensics-evidence-chain-of-custody/ccrims/index.php
```


### Step 5: Demo Credentials


The SQL file contains clearly labeled synthetic demo accounts. Replace them before production use and change every demonstration password.


## 🔒 Security Limitations


This is a security-architecture prototype rather than a certified evidence-management platform. A production deployment would additionally require secure file storage, signed audit records, encrypted backups, centralized logging, formal identity management, and independent forensic review.


## 👨‍💻 Author


**Muhammad Bilal**

- BS Cyber Security Undergraduate
- Specializing in digital forensics, application security, and defensive systems engineering.
