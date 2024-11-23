<?php

namespace App\Domain;

class Book
{
    public function __construct(
        private string $title,
        private string $author,
        private string $isbn,
        private int $year,
        private string $description = ''
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'year' => $this->year,
            'description' => $this->description,
        ];
    }

    function sanitizeBook(): Book
    {
        return new Book(
            htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->author, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->isbn, ENT_QUOTES, 'UTF-8'),
            $this->year,
            htmlspecialchars($this->description, ENT_QUOTES, 'UTF-8')
        );
    }
}
