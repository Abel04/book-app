<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\{BookCommandHandler, BookQueryHandler};
use App\Infrastructure\{BookRepository, DatabaseConnection, ExternalBookService};
use GuzzleHttp\Client;

$connection = DatabaseConnection::getInstance()->getConnection();
$repository = new BookRepository($connection);

$connection->exec("
    CREATE TABLE IF NOT EXISTS books (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        author TEXT,
        isbn TEXT,
        year INTEGER,
        description TEXT
    )
");

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Content-Security-Policy: default-src \'self\';');

// Extract the HTTP method and request URI
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
parse_str(parse_url($path, PHP_URL_QUERY), $queryParams);

// Routing
if ($method === 'GET') {
    $queryHandler = new BookQueryHandler($repository);
    if ($path === '/books') { // Retrieve all books
        $queryHandler->getBooks();
    } elseif (preg_match('#^/books/([^/]+)$#', $path, $matches)) { // Retrieve a single book by ISBN
        $isbn = $matches[1];
        $queryHandler->getBook($isbn);
    }
    elseif (preg_match('#^/search#', $path)) {
        parse_str($_SERVER['QUERY_STRING'], $queryParams);
        if (!isset($queryParams['q']) || empty($queryParams['q'])) {
            echo json_encode(['error' => 'Search query is required']);
            http_response_code(400);
            exit;
        }
        echo json_encode($queryHandler->searchBooks($queryParams['q']));
    } else { // Invalid route
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
else {
    $externalService = new ExternalBookService(new Client());
    $commandHandler = new BookCommandHandler($repository, $externalService);

    if ($path === '/books' && $method === 'POST') { // Create a new book
        $data = json_decode(file_get_contents('php://input'), true);
        $commandHandler->createBook($data);
    } elseif (preg_match('#^/books/([^/]+)$#', $path, $matches) && $method === 'DELETE') { // Delete a book by ISBN
        $isbn = $matches[1];
        $commandHandler->deleteBook($isbn);
    } elseif (preg_match('#^/books/([^/]+)$#', $path, $matches) && $method === 'PUT') { // Update a book by ISBN
        $isbn = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $commandHandler->updateBook($isbn, $data);
    } else { // Invalid route
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
