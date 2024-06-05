<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\UploadedFile;


class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_book()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $data = [
            'title' => 'Sample Book',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ];

        $this->postJson('/api/books', $data, ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(201)
            ->assertJson($data);
    }

    public function test_can_list_books()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Book::factory()->count(5)->create();

        $this->getJson('/api/books', ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'publication_year', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_can_show_book()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $book = Book::factory()->create();

        $this->getJson("/api/books/{$book->id}", ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson($book->toArray());
    }

    public function test_can_update_book()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $book = Book::factory()->create();
        $data = [
            'title' => 'Updated Title',
            'author' => 'Jane Doe',
            'publication_year' => 2024,
        ];

        $this->putJson("/api/books/{$book->id}", $data, ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson($data);
    }

    public function test_can_delete_book()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $book = Book::factory()->create();
        $bookId = $book->id;

        $this->deleteJson("/api/books/{$bookId}", [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(204);
    }

    public function test_can_create_book_with_image()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $data = [
            'title' => 'Sample Book',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ];

        $image = UploadedFile::fake()->image('book_cover.jpg');

        $this->postJson('/api/books', array_merge($data, ['cover_image_path' => $image]), ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(201)
            ->assertJson($data);
    }

    public function test_can_update_book_with_image()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $book = Book::factory()->create();
        $data = [
            'title' => 'Updated Title',
            'author' => 'Jane Doe',
            'publication_year' => 2024,
        ];

        $image = UploadedFile::fake()->image('book_cover.jpg');

        $this->putJson("/api/books/{$book->id}", array_merge($data, ['cover_image' => $image]), ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson($data);
    }
}
