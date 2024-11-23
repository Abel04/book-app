# PHP Book Management API

A simple PHP-based application for managing a list of books with CRUD functionality. The application integrates with the Open Library API to fetch additional book information and implements modern software architecture principles like SOLID, CQRS, and DDD.

## Features
* CRUD Operations: Create, read, update, and delete books.
* External API Integration: Fetch book details from Open Library API.
* Search Books: Search books by title or author in the local database.
* CQRS: Commands (create, update, delete) are separated from queries (read, search).
* Security: Protects against SQL injection, XSS, and CSRF.
* Logging: Logs critical application events such as errors.
* Unit Tests

## Requirements
* PHP 8.1 or higher
* Composer
* SQLite database
* Postman

## Installation
1. Install dependencies:
```
composer install
```
2. Run:
```
php -S 0.0.0.0:8000 -t public
```

3. Test with Postman the endpoints in http://localhost:8000
* GET /books Fetch all books.
* GET /books/{isbn} Fetch a single book by ISBN.
* POST /books Add a new book. Requires JSON body
```
{
  "title": "Book Title",
  "author": "Author Name",
  "isbn": "123456789",
  "year": 2023
}
```
* PUT /books/{isbn} Update book details. Requires JSON body
* DELETE /books/{isbn} Delete a book by ISBN.
* GET /search?q={string} Search books by title or author:

## Run Tests Locally
```
vendor/bin/phpunit
```
Tests should pass with no errors.

## License

This project is licensed under the MIT License.