# Patient CRUD API Documentation

This document provides details on how to use the Patient CRUD API endpoints in the multi-tenant Laravel application.

## Base URL

All endpoints are prefixed with `/api` and require tenant authentication.

## Authentication

All patient endpoints require a valid tenant token in the `Authorization` header:

```
Authorization: Bearer [token]
```

To obtain a token, first authenticate with the tenant login endpoint:

```
POST /api/tenant/login
```

## API Endpoints

### 1. Get All Patients

Retrieve all patients for the authenticated tenant.

**Endpoint:** `GET /api/patients`

**Headers:**

```
Authorization: Bearer [token]
```

**Response:**

```json
[
    {
        "POID": 1,
        "RegNo": "REG001",
        "Pname": "John Doe",
        "Paddress": "123 Main St",
        "Pcontact": "555-1234",
        "Pgender": "Male",
        "Page": "30",
        "DrOID": 1,
        "Tital": "Mr.",
        "photo": null,
        "MemberID": 1001,
        "AdharNo": "1234-5678-9012",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
]
```

**Example using cURL:**

```bash
curl -X GET http://localhost/api/patients \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9"
```

### 2. Create a New Patient

Create a new patient record.

**Endpoint:** `POST /api/patients`

**Headers:**

```
Authorization: Bearer [token]
Content-Type: application/json
```

**Request Body:**

```json
{
    "RegNo": "REG002",
    "Pname": "Jane Smith",
    "Paddress": "456 Oak Ave",
    "Pcontact": "555-5678",
    "Pgender": "Female",
    "Page": "25",
    "DrOID": 2,
    "Tital": "Ms.",
    "photo": null,
    "MemberID": 1002,
    "AdharNo": "9876-5432-1098"
}
```

**Response:**

```json
{
    "POID": 2,
    "RegNo": "REG002",
    "Pname": "Jane Smith",
    "Paddress": "456 Oak Ave",
    "Pcontact": "55-5678",
    "Pgender": "Female",
    "Page": "25",
    "DrOID": 2,
    "Tital": "Ms.",
    "photo": null,
    "MemberID": 1002,
    "AdharNo": "9876-5432-1098",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

**Example using cURL:**

```bash
curl -X POST http://localhost/api/patients \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9" \
  -H "Content-Type: application/json" \
  -d '{
    "RegNo": "REG002",
    "Pname": "Jane Smith",
    "Paddress": "456 Oak Ave",
    "Pcontact": "555-5678",
    "Pgender": "Female",
    "Page": "25",
    "DrOID": 2,
    "Tital": "Ms.",
    "photo": null,
    "MemberID": 1002,
    "AdharNo": "9876-5432-1098"
  }'
```

### 3. Get a Specific Patient

Retrieve a specific patient by ID.

**Endpoint:** `GET /api/patients/{id}`

**Headers:**

```
Authorization: Bearer [token]
```

**Response:**

```json
{
    "POID": 1,
    "RegNo": "REG001",
    "Pname": "John Doe",
    "Paddress": "123 Main St",
    "Pcontact": "555-1234",
    "Pgender": "Male",
    "Page": "30",
    "DrOID": 1,
    "Tital": "Mr.",
    "photo": null,
    "MemberID": 1001,
    "AdharNo": "1234-5678-9012",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00.000000Z"
}
```

**Example using cURL:**

```bash
curl -X GET http://localhost/api/patients/1 \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9"
```

### 4. Update a Patient

Update an existing patient record.

**Endpoint:** `PUT /api/patients/{id}`

**Headers:**

```
Authorization: Bearer [token]
Content-Type: application/json
```

**Request Body:**

```json
{
    "RegNo": "REG001-UPDATED",
    "Pname": "John Doe Updated",
    "Paddress": "789 Pine St",
    "Pcontact": "555-9999",
    "Pgender": "Male",
    "Page": "31",
    "DrOID": 1,
    "Tital": "Mr.",
    "photo": null,
    "MemberID": 1001,
    "AdharNo": "1234-5678-9012"
}
```

**Response:**

```json
{
    "POID": 1,
    "RegNo": "REG001-UPDATED",
    "Pname": "John Doe Updated",
    "Paddress": "789 Pine St",
    "Pcontact": "555-9999",
    "Pgender": "Male",
    "Page": "31",
    "DrOID": 1,
    "Tital": "Mr.",
    "photo": null,
    "MemberID": 1001,
    "AdharNo": "1234-5678-9012",
    "created_at": "2023-01-01T00:00.000000Z",
    "updated_at": "2023-01-02T00:0:00.000000Z"
}
```

**Example using cURL:**

```bash
curl -X PUT http://localhost/api/patients/1 \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9" \
  -H "Content-Type: application/json" \
  -d '{
    "RegNo": "REG001-UPDATED",
    "Pname": "John Doe Updated",
    "Paddress": "789 Pine St",
    "Pcontact": "555-9999",
    "Pgender": "Male",
    "Page": "31",
    "DrOID": 1,
    "Tital": "Mr.",
    "photo": null,
    "MemberID": 1001,
    "AdharNo": "1234-5678-9012"
  }'
```

### 5. Delete a Patient

Delete a patient record.

**Endpoint:** `DELETE /api/patients/{id}`

**Headers:**

```
Authorization: Bearer [token]
```

**Response:**

```json
{
    "message": "Patient deleted successfully"
}
```

**Example using cURL:**

```bash
curl -X DELETE http://localhost/api/patients/1 \
  -H "Authorization: Bearer eyJ0ZW5hbnRfaWQiOjEsImRiX25hbWUiOiJ0ZW5hbnRfZGIiLCJpYXQiOjE2NzI1MzExOTl9"
```

## Error Responses

All endpoints may return the following error responses:

-   `400 Bad Request` - Missing tenant connection
-   `401 Unauthorized` - Invalid or missing token
-   `404 Not Found` - Patient not found
-   `422 Unprocessable Entity` - Validation errors

Example error response:

```json
{
    "error": "Patient not found"
}
```
