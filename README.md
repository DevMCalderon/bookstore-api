# Bookstore API

## Overview
This is a RESTful API built with Laravel for managing a bookstore. Users can register, log in, and perform CRUD operations on books they own. The API uses Laravel Sanctum for token-based authentication, ensuring secure access to protected endpoints. It’s designed for simplicity and scalability, ideal for a backend developer interview or a small-scale production app.

## Requirements
- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Laravel**: 10.x or higher
- **Database**: SQLite (for simplicity, configurable in `.env`)
- **Optional**: Postman or cURL for testing API endpoints

## Setup Instructions
1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd bookstore-api
   ```
2. **Install Dependencies**:
    Run the following command to install PHP and Laravel dependencies:
    ```bash
    composer install
    ```
3. **Configure Environment**:
    Copy the example environment file and set up SQLite:
    ```bash
    cp .env.example .env
    touch database/database.sqlite
    ```
    Edit `.env` to set:
    ```env
    DB_CONNECTION=sqlite
    DB_DATABASE=/absolute/path/to/database/database.sqlite
    ```
4. **Run Migrations**:
   Create the database tables (users, books, personal_access_tokens):
    ```bash
    php artisan migrate
    ```
5. **Seed the Database**:
   Populate with sample data:
   ```bash
    php artisan db:seed
    ```
6. **Install Sanctum:**:
   Ensure Laravel Sanctum is installed
   ```bash
    composer require laravel/sanctum
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan migrate
    ```

## Running the Project
1. Start the Laravel development server:
   ```bash
    php artisan serve
    ```
    The API will be available at `http://localhost:8000`.
2. Test endpoints using Postman or cURL (see API Endpoints below).

## API Endpoints
All endpoints are prefixed with `/api`. Protected endpoints require an `Authorization: Bearer <token>` header, where `<token>` is obtained from `/login` or `/register`.

### Authentication
- #### POST `/api/register`
  Registers a new user and returns a token. 
  Request Body:
  ```json
    {
        "name": "Test User",
        "email": "test@example.com",
        "password": "password123",
        "password_confirmation": "password123"
    }
    ```
    Response (201 Created):
     ```json
    {
        "user": { "id": 1, "name": "Test User", "email": "test@example.com" },
        "token": "1|randomTokenString"
    }
    ```
- #### POST `/api/login`
  Authenticates a user and returns a token. 
  Request Body:
   ```json
    {
        "email": "test@example.com",
        "password": "password123"
    }
    ```
    Response (200 OK):
     ```json
    {
        "user": { "id": 1, "name": "Test User", "email": "test@example.com" },
        "token": "1|randomTokenString"
    }
    ```
    Error (401 Unauthorized):
    ```json
    { "error": "Credenciales inválidas" }
    ```
### Books (Protected)
All require `Authorization: Bearer <token>` header.
- #### GET `/api/books`
Lists paginated books owned by the authenticated user (10 per page).
Response (200 OK):
```json
[
    { "id": 1, "title": "Book Title", "author": "Author Name", "description": "Desc", "price": 19.99, "user_id": 1 },
    ...
]
```
- #### POST `/api/books`
Creates a new book for the authenticated user.
Request Body:
```json
{
    "title": "New Book",
    "author": "Author Name",
    "description": "A great book",
    "price": 29.99
}
```
Response (201 Created):
```json
{ "id": 1, "title": "New Book", "author": "Author Name", "description": "A great book", "price": 29.99, "user_id": 1 }
```
- #### GET `/api/books/{id}`
Retrieves a specific book by `ID` (if owned by the user).
Response (200 OK):
```json
{ "id": 1, "title": "Book Title", "author": "Author Name", "description": "Desc", "price": 19.99, "user_id": 1 }
```
Error (403 Forbidden or 404 Not Found):
```json
{ "error": "No autorizado" }
```
- #### PUT/PATCH `/api/books/{id}`
Updates a specific book (if owned by the user).
Request Body (partial updates allowed with PATCH):
```json
{
    "title": "Updated Title",
    "price": 24.99
}
```
Response (200 OK):
```json
{ "id": 1, "title": "Updated Title", "author": "Author Name", "description": "Desc", "price": 24.99, "user_id": 1 }
```
- #### DELETE `/api/books/{id}`
Deletes a specific book (if owned by the user).
Response (204 No Content): Empty body.
Error (403 or 404):
```json
{ "error": "No autorizado" }
```

## Testing the API
1. Use Postman or cURL to test endpoints.
2. Register a user (`POST /api/register`) to get a token.
3. Log in (`POST /api/login`) to authenticate and get a token.
4. Use the token in the `Authorization: Bearer <token>` header for book-related endpoints.
5. Run unit/feature tests:
```bash
php artisan test
```
Tests are located in `tests/Feature/AuthTest.php` (for auth) and `tests/Unit/BookTest.php` (for books).

## Notes
- Ensure the `User` model has the `HasApiTokens` trait from Sanctum.
- Books are scoped to the authenticated user (`user_id`).
- Error handling is implemented for invalid credentials, unauthorized access, and missing resources.
- For production, configure a proper database (e.g., MySQL/PostgreSQL) and secure the `.env` file.