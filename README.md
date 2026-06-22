# Hostel Management System

## Project Overview

This repository contains an existing university Hostel Management System used to manage hostel applications, room and bed allocation, hostel registration periods, payments, repairs, wardens, and user accounts.

This is a live university system. Changes must be minimal, reviewed carefully, and tested safely before deployment. Do not refactor, rename, move, or reformat existing application files unless there is an approved issue that requires it.

## Tech Stack

- PHP server-rendered web application
- MySQL/MariaDB database accessed through `mysqli`
- Apache/WAMP style local hosting
- Bootstrap, jQuery, Font Awesome, and custom CSS/JavaScript
- Composer dependencies for mail, OAuth, and PDF generation
- Optional Dockerfile based on `php:8.3-apache`

## Folder Structure

```text
.
|-- account/              # Login, logout, registration, password reset
|-- connection/           # Local database connection configuration
|-- css/                  # Bootstrap and custom stylesheets
|-- img/                  # Static image assets
|-- js/                   # Bootstrap, jQuery, validation, and custom scripts
|-- sql/                  # Local database dumps; not committed because they may contain private data
|-- *.php                 # Main application pages and workflow handlers
|-- composer.json         # PHP dependency definitions
|-- composer.lock         # Locked PHP dependency versions
|-- Dockerfile            # Optional Apache/PHP container definition
```

## Main Modules and Features

- Student hostel application submission
- Hostel registration window management
- Eligibility review and student selection
- Room and bed allocation
- Current hostel resident views
- Payment upload and review support
- Repair request submission and tracking
- Warden management and hostel block allocation
- User account management
- Google OAuth/mail integration for notification-related workflows
- PDF generation for selected workflows

## User Roles

The application stores the logged-in role in the PHP session, mainly through `$_SESSION["cat"]`.

- `1` - Student
- `2` - Sub-warden
- `3` - Hostel secretary
- Additional supervisor-related logic is referenced in the code and should be checked before role changes.

Student login uses an external student data endpoint through `getData.php`. Staff login uses local records in the `users` table with hashed passwords.

## Setup Instructions

1. Place the project in a PHP/Apache web root, for example a WAMP `www` directory.
2. Install Composer dependencies:

   ```bash
   composer install
   ```

3. Configure the local database connection file at `connection/connect.php`.
4. Configure OAuth/mail settings in `config.php` only through a secure local or deployment-specific process.
5. Restore or create the MySQL/MariaDB database using an approved sanitized schema or a secure production backup handled outside git.
6. Open the application through the configured local web server.

## Environment and Configuration Notes

Do not commit real environment files, database passwords, OAuth client secrets, access tokens, or local server configuration. The following files are intentionally treated as local/deployment-specific:

- `connection/connect.php`
- `config.php`
- OAuth client secret JSON files
- token backup JSON files
- temporary curl or URL files

Keep production values in a secure password manager, deployment secret store, or server-only configuration process.

## Database Notes

The application uses a MySQL/MariaDB database with tables for academic years, hostels, rooms, beds, registrations, repairs, users, user categories, wardens, and OAuth tokens.

Local SQL dump files are ignored because they may contain student records, emails, contact details, medical/payment document references, user hashes, and OAuth token data. Only sanitized schema/data should be shared through git, and only after review.

Do not run migrations or change the database structure without explicit approval and a verified rollback plan.

## Safety Workflow for Future Changes

1. Start from a clean git status.
2. Read the relevant files before editing.
3. Make the smallest possible change for the approved issue.
4. Avoid unrelated formatting or refactoring.
5. Test on local or staging data, not directly against production.
6. Review `git diff` carefully before committing.
7. Confirm no secrets, dumps, logs, uploaded files, or generated private data are staged.

## Repository Policy

This repository is intended as a safe source backup for the existing Hostel Management System. Application logic should remain unchanged unless a specific supervisor-approved issue is being handled.
