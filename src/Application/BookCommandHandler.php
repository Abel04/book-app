<?php

namespace App\Application;

use App\Domain\{Book, BookRepositoryInterface};
use App\Infrastructure\{ExternalBookService, Logger};

class BookCommandHandler
{
    public function __construct(
        private BookRepositoryInterface $repository,
        private ExternalBookService $externalService
    ) {}

    public function createBook(array $data)
    {
        try {
            $description = $this->externalService->getBookDescription($data['isbn']);
            
            $data['description'] = $description;

            $book = new Book($data['title'], $data['author'], $data['isbn'], (int)$data['year'], $data['description']);
            $this->repository->create($book);

            Logger::getLogger()->info('Book created successfully.', ['isbn' => $data['isbn']]);
            echo json_encode(['message' => 'Book created successfully']);
        } catch (\Exception $e) {
            Logger::getLogger()->error('Unable to create new book', [
                'error' => $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteBook(string $isbn)
    {
        $this->repository->delete($isbn);
        Logger::getLogger()->info('Book deleted successfully.', ['isbn' => $isbn]);
        echo json_encode(['message' => "Book with ISBN $isbn deleted successfully"]);
    }

    public function updateBook(string $isbn, array $data)
    {
        $existingBook = $this->repository->findById($isbn);
        if (!$existingBook) {
            Logger::getLogger()->error('Book not found', [
                'error' => "Book with ISBN $isbn not found"
            ]);
            http_response_code(404);
            echo json_encode(['error' => "Book with ISBN $isbn not found"]);
            return;
        }

        try {
            $description = $data['description'] ?? $this->externalService->getBookDescription($isbn);
            $updatedBook = new Book($data['title'], $data['author'], $isbn, (int)$data['year'], $description);
            $this->repository->update($updatedBook);
            Logger::getLogger()->info('Book updated successfully.', ['isbn' => $isbn]);

            echo json_encode(['message' => "Book with ISBN $isbn updated successfully"]);
        } catch (\Exception $e) {
            Logger::getLogger()->error("Error updating book $isbn", [
                'error' => $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}