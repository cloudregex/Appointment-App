# php artisan db:seed

# Multi-Tenant Laravel API

This is a multi-tenant Laravel application with API endpoints for tenant authentication and user management.

## API Endpoints

### 1. Tenant Login

Authenticate a tenant and receive a token for subsequent requests.

**Endpoint:** `POST /api/tenant/login`

**Request Body:**

```json
{
    "db_host": "127.0.0.1",
    "db_port": "3306",
    "db_name": "appointmentapp",
    "db_username": "appointmentapp",
    "db_password": "appointmentapp"
}
```

**Response:**

```json
{
    "token": "base64_encoded_token",
    "tenant": {
        "id": 1,
        "db_host": "localhost",
        "db_port": "306",
        "db_name": "tenant_database",
        "db_username": "tenant_user",
        "db_password": "tenant_password",
        "created_at": "2023-01-01T00:00:00.00000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Login successful"
}
```

**Example using cURL:**

```bash
curl -X POST http://localhost/api/tenant/login \
  -H "Content-Type: application/json" \
  -d '{
    "db_host": "localhost",
    "db_port": "3306",
    "db_name": "tenant_database",
    "db_username": "tenant_user",
    "db_password": "tenant_password"
  }'
```

### 2. Get Tenant Users

Retrieve all users for the authenticated tenant.

**Endpoint:** `GET /api/users`

**Headers:**

```http
Authorization: Bearer [token_from_login]
```

**Response:**

```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "email_verified_at": null,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
]
```

**Example using cURL:**

```bash
curl -X GET http://localhost/api/users \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9"
```

## Authentication Flow

1. **Login**: Make a POST request to `/api/tenant/login` with database credentials
2. **Token**: Receive a base64-encoded token containing the tenant ID
3. **Access**: Use the token in the `Authorization: Bearer [token]` header for subsequent requests
4. **Middleware**: The TenantMiddleware will validate the token and set up the tenant database connection

## Database Seeding

To populate the database with dummy users, run:

```bash
php artisan db:seed
```

This will create 50 dummy users in the main users table and 50 dummy users in each tenant's database.
