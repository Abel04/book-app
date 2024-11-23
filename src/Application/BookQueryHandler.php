<?php

namespace App\Application;

use App\Domain\{BookRepositoryInterface};

class BookQueryHandler
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {}

    public function getBooks()
    {
        $books = $this->repository->findAll();

        echo json_encode(array_map(function ($book) {
            return [
                'id' => (int)$book['id'],
                'title' => htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'),
                'author' => htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'),
                'isbn' => htmlspecialchars($book['isbn'], ENT_QUOTES, 'UTF-8'),
                'year' => (int)$book['year'],
                'description' => htmlspecialchars($book['description'], ENT_QUOTES, 'UTF-8'),
            ];
        }, $books));
    }

    public function getBook(string $isbn)
    {
        $book = $this->repository->findById($isbn);

        if (!$book) {
            http_response_code(404);
            echo json_encode(['error' => "Book with ISBN $isbn not found"]);
            return;
        }

        echo json_encode($book->sanitizeBook()->toArray());
    }

    public function searchBooks(string $query): array
    {
        return $this->repository->searchBooks($query);
    }
}
