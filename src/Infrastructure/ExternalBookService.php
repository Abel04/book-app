<?php

namespace App\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ExternalBookService
{
    public function __construct(private Client $client) {}

    public function getBookDescription(string $isbn): string
    {
        try {
            $response = $this->client->get("https://openlibrary.org/api/books", [
                'query' => [
                    'bibkeys' => "ISBN:$isbn",
                    'format' => 'json',
                    'jscmd' => 'data'
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            if (!isset($data["ISBN:$isbn"])) {
                throw new \Exception('Book information not found for the provided ISBN.');
            }

            return $data["ISBN:$isbn"]['key'] ?? ''; // Using 'key' here as the openlibrary doesn't return a description
        } catch (RequestException $e) {
            Logger::getLogger()->error('Error consulting external API.', [
                'error' => $e->getMessage(),
                'isbn' => $isbn,
            ]);
            throw new \Exception('Failed to fetch book information from the external API. Please try again later.');
        } catch (\Exception $e) {
            Logger::getLogger()->error('Error consulting external API.', [
                'error' => $e->getMessage(),
                'isbn' => $isbn,
            ]);
            throw $e; // Re-throw the exception for the controller to handle
        }
    }
}
