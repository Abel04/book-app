<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Book;

class BookTest extends TestCase
{
    public function testBookToArray()
    {
        $book = new Book(
            'Test Title',
            'Test Author',
            '978-1-60309-452-8',
            2023,
            'This is a test description.'
        );

        $expected = [
            'title' => 'Test Title',
            'author' => 'Test Author',
            'isbn' => '978-1-60309-452-8',
            'year' => 2023,
            'description' => 'This is a test description.',
        ];

        $this->assertSame($expected, $book->toArray());
    }
}
