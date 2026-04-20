# Mini LinkedIn API

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![JWT](https://img.shields.io/badge/Auth-JWT-green)

Backend API for a recruitment platform connecting candidates and recruiters.
Candidates can create profiles, add skills, and apply to job offers.
Recruiters can post offers and manage applications.
Administrators supervise the entire platform.

Built with Laravel 12, JWT authentication, and role-based access control.

---

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+
- XAMPP or equivalent

---

## Installation

```bash
git clone https://github.com/yassintgn18/mini-linkedin.git
cd mini-linkedin
composer install
cp .env.example .env
php artisan key:generate
```

Then open `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_linkedin
DB_USERNAME=root
DB_PASSWORD=
```

Then generate the JWT secret:

```bash
php artisan jwt:secret
```

Then run migrations and seed the database:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

---

## Test Accounts

After seeding, emails are randomly generated but the password is `password` for all users.
Use the `/api/login` endpoint with any seeded email from your database and password `password`.

Roles available: `admin`, `recruteur`, `candidat`

---

## Authentication

This API uses JWT. After login you receive a token. Include it in every protected request:
Authorization: Bearer your_token_here

---

## API Routes

### Authentication

| Method | Endpoint      | Role   | Description                    |
| ------ | ------------- | ------ | ------------------------------ |
| POST   | /api/register | Public | Register a new user            |
| POST   | /api/login    | Public | Login and get JWT token        |
| GET    | /api/me       | Auth   | Get current authenticated user |
| POST   | /api/logout   | Auth   | Logout and invalidate token    |
| POST   | /api/refresh  | Auth   | Refresh JWT token              |

### Profile Management

| Method | Endpoint                             | Role     | Description                |
| ------ | ------------------------------------ | -------- | -------------------------- |
| POST   | /api/profil                          | Candidat | Create profile (once only) |
| GET    | /api/profil                          | Candidat | View own profile           |
| PUT    | /api/profil                          | Candidat | Update own profile         |
| POST   | /api/profil/competences              | Candidat | Add skill to profile       |
| DELETE | /api/profil/competences/{competence} | Candidat | Remove skill from profile  |

### Job Offers

| Method | Endpoint         | Role                   | Description                              |
| ------ | ---------------- | ---------------------- | ---------------------------------------- |
| GET    | /api/offres      | Public                 | List active offers (paginated, filtered) |
| GET    | /api/offres/{id} | Public                 | Get offer details                        |
| POST   | /api/offres      | Recruteur              | Create a new offer                       |
| PUT    | /api/offres/{id} | Recruteur (owner only) | Update own offer                         |
| DELETE | /api/offres/{id} | Recruteur (owner only) | Delete own offer                         |

Filters supported: `localisation`, `type` (CDI, CDD, stage)
Pagination: 10 offers per page, sorted by creation date.

### Applications

| Method | Endpoint                      | Role                   | Description                |
| ------ | ----------------------------- | ---------------------- | -------------------------- |
| POST   | /api/offres/{id}/candidater   | Candidat               | Apply to a job offer       |
| GET    | /api/mes-candidatures         | Candidat               | View own applications      |
| GET    | /api/offres/{id}/candidatures | Recruteur (owner only) | View applications received |
| PATCH  | /api/candidatures/{id}/statut | Recruteur (owner only) | Change application status  |

Statuses: `en_attente`, `acceptee`, `refusee`

### Administration

| Method | Endpoint               | Role  | Description                  |
| ------ | ---------------------- | ----- | ---------------------------- |
| GET    | /api/admin/users       | Admin | List all users               |
| DELETE | /api/admin/users/{id}  | Admin | Delete a user account        |
| PATCH  | /api/admin/offres/{id} | Admin | Activate or deactivate offer |

---

## Events & Listeners

The platform uses Laravel Events & Listeners to decouple application logic.

- `CandidatureDeposee` — fired when a candidate applies to an offer. Logs the date, candidate name, and offer title to `storage/logs/candidatures.log`.
- `StatutCandidatureMis` — fired when a recruiter changes an application status. Logs the old status, new status, and date to the same file.

---

## Postman Collection

Import `/postman/mini-linkedin.json` into Postman to test all endpoints.
The collection covers: registration, login, full profile CRUD, offers CRUD, applications, status changes, and error cases (401, 403, 422).

---

## Git Workflow

Each feature was developed on a dedicated branch and integrated via Pull Request into `main`.
Branch protection rules were enforced — all PRs required at least one approval before merging.

> **Note:** One branch (`feature/candidatures-admin`) contained two features due to an initial misunderstanding of the Git workflow. The team identified this mistake, corrected the approach for all subsequent branches, and chose to document it here as a learning point.

---

## Authors

- **Yassin Touggani** — Database (migrations, models, seeders), JWT authentication & role middleware, Events logging configuration, Project setup & Git management

- **Kévin CATARYA** — Profile management (3.1), Job offers (3.2), Events & Listeners (Part 4), Postman collection (success and error tests, request name format: Error{code} - request role - user role)

- **Younes Benamar** — Applications (3.3), Administration (3.4), Event dispatching in controllers