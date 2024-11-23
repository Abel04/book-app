<?php

namespace App\Domain;

interface BookRepositoryInterface
{
    public function findAll(): array;
    public function findById(string $isbn): ?Book;
    public function create(Book $book): void;
    public function update(Book $book): void;
    public function delete(string $isbn): void;
    public function searchBooks(string $query): array;
}
