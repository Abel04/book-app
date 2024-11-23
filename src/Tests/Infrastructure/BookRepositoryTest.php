<?php

use PHPUnit\Framework\TestCase;
use App\Infrastructure\BookRepository;
use App\Domain\Book;

class BookRepositoryTest extends TestCase
{
    private $pdo;
    private $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE books (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT,
                author TEXT,
                isbn TEXT,
                year INTEGER,
                description TEXT
            )
        ");

        $this->repository = new BookRepository($this->pdo);
    }

    public function testAddAndFindBook()
    {
        $book = new Book(
            'Test Title',
            'Test Author',
            '978-1-60309-452-8',
            2023,
            'This is a test description.'
        );

        $this->repository->create($book);
        $retrievedBook = $this->repository->findById('978-1-60309-452-8');

        $this->assertNotNull($retrievedBook);
        $this->assertSame($book->toArray(), $retrievedBook->toArray());
    }

    public function testSearchBooks()
    {
        $book1 = new Book('First Title', 'Author One', '978-1-60309-452-8', 2023, 'Description One');
        $book2 = new Book('Second Title', 'Author Two', '978-1-60309-452-9', 2023, 'Description Two');

        $this->repository->create($book1);
        $this->repository->create($book2);

        $results = $this->repository->searchBooks('Second');

        $this->assertCount(1, $results);
        $this->assertSame('Second Title', $results[0]['title']);
    }
}
