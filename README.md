# Client Management API

A Laravel 13 and MySQL backend for managing clients, implemented as a Laravel
Modules `Client` module with a repository layer.

## Requirements

- PHP 8.3 or later with `pdo_mysql`
- Composer 2.10.3 or later
- MySQL 8.0 or later

## Setup

1. Create a MySQL database named `client_management`.
2. Install dependencies and configure the environment:

   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   ```

   Set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`
   in `.env` to match your MySQL server.
3. Run migrations and start the API:

   ```bash
   php artisan migrate
   php artisan serve
   ```

The API is available at `http://127.0.0.1:8000/api/v1`.

### Connecting a frontend

Set `CORS_ALLOWED_ORIGINS` in `.env` to a comma-separated list of the
frontend's exact origins, including scheme and port. By default, Vite
(`localhost` or `127.0.0.1` on port `5173`) and common React development
servers (port `3000`) are allowed. Restart the backend after changing this
setting.

Use `http://127.0.0.1:8000/api/v1` as the API base URL; for example:

```js
const apiBaseUrl = 'http://127.0.0.1:8000/api/v1';

const response = await fetch(`${apiBaseUrl}/clients`);
const clients = await response.json();
```

The current project contains the backend only. To wire a specific frontend
client or set additional CORS origins, add its project and origin.

## Endpoints

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/clients` | List clients (15 per page) |
| `POST` | `/clients` | Create a client and calculate pricing |
| `GET` | `/clients/search?phone={phone}` | Find a client by exact phone number |
| `GET` | `/clients/{id}` | Get one client |
| `PUT`, `PATCH` | `/clients/{id}` | Update phone, address, and/or project budget |
| `DELETE` | `/clients/{id}` | Delete a client |

Validation failures return `422` JSON responses; missing clients return `404`.
List responses include Laravel pagination metadata. Client responses include
the original fields, employee count, and all calculated pricing fields.

### Create a client

```json
{
  "name": "Ada Lovelace",
  "email": "ada@example.com",
  "phone": "+1-555-0100",
  "company": "Analytical Engines Ltd",
  "company_employee_count": 101,
  "address": "10 Example Street",
  "project_budget": "1000000.00"
}
```

`company_employee_count` is required because the discount rules depend on
company size, which cannot be derived from the company name. It is recorded
when the client is created and is not editable through the requested edit
fields.

## Pricing rules

- Budget of at least `1,000,000`: service charge is 10%.
- Budget of at least `500,000` and below `1,000,000`: service charge is 5%.
- Budget below `500,000`: service charge is 2%.
- More than 100 employees: discount is 5%.
- Fewer than 100 employees: discount is 2%.
- Exactly 100 employees: no discount.
- Final project value = budget + service charge - discount.

All pricing is rounded to two decimal places and stored in MySQL decimal
columns.

## Tests

Run the feature and unit tests with:

```bash
php artisan test
```
