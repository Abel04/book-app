<?php

namespace App\Infrastructure;

use App\Domain\Book;
use App\Domain\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(private \PDO $connection) {}

    public function findAll(): array
    {
        $stmt = $this->connection->query('SELECT * FROM books');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(string $isbn): ?Book
    {
        $stmt = $this->connection->prepare('SELECT * FROM books WHERE isbn = :isbn');
        $stmt->execute(['isbn' => $isbn]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $data ? new Book($data['title'], $data['author'], $data['isbn'], (int) $data['year'], $data['description']) : null;
    }

    public function create(Book $book): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO books (title, author, isbn, year, description) VALUES (:title, :author, :isbn, :year, :description)'
        );
        $stmt->execute($book->toArray());
    }

    public function update(Book $book): void
    {
        $stmt = $this->connection->prepare(
            'UPDATE books SET title = :title, author = :author, year = :year, description = :description WHERE isbn = :isbn'
        );
        $stmt->execute($book->toArray());
    }

    public function delete(string $isbn): void
    {
        $stmt = $this->connection->prepare('DELETE FROM books WHERE isbn = :isbn');
        $stmt->execute(['isbn' => $isbn]);
    }

    public function searchBooks(string $query): array
    {
        $sql = "SELECT * FROM books WHERE title LIKE :query OR author LIKE :query";
        $stmt = $this->connection->prepare($sql);
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':query', $searchTerm, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
