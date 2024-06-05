<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function test_can_create_book_instance()
    {
        $book = new Book([
            'title' => 'Sample Book',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ]);

        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('Sample Book', $book->title);
        $this->assertEquals('John Doe', $book->author);
        $this->assertEquals(2023, $book->publication_year);
    }

    public function test_can_update_book_instance()
    {
        $book = new Book([
            'title' => 'Sample Book',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ]);

        $book->title = 'Updated Title';
        $book->author = 'Jane Doe';
        $book->publication_year = 2024;

        $this->assertEquals('Updated Title', $book->title);
        $this->assertEquals('Jane Doe', $book->author);
        $this->assertEquals(2024, $book->publication_year);
    }

    public function test_can_read_book_properties()
    {
        $book = new Book([
            'title' => 'Sample Book',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ]);

        $this->assertEquals('Sample Book', $book->title);
        $this->assertEquals('John Doe', $book->author);
        $this->assertEquals(2023, $book->publication_year);
    }

    public function test_can_delete_book_instance()
    {
        $book = Book::factory()->create([
            'title' => 'SampleBook',
            'author' => 'John Doe',
            'publication_year' => 2023,
        ]);

        $this->assertTrue($book->delete());
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

        $response = $this->postJson('/api/books', array_merge($data, ['cover_image' => $image]), ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(201)->assertJson($data);
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

        $updated_image = UploadedFile::fake()->image('updated_book_cover.jpg');

        $response = $this->putJson("/api/books/{$book->id}", array_merge($data, ['cover_image_path' => $updated_image]), ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson($data);

        $updated_book = Book::find($book->id);
        $this->assertTrue($updated_book->cover_image_path !== null);

        $response->assertJsonFragment(['cover_image_path' => $updated_book->cover_image_path]);
    }

    // public function test_can_delete_book_with_image()
    // {

    //     $user = User::factory()->create();
    //     $token = JWTAuth::fromUser($user);

    //     $book = Book::factory()->create();
    //     $bookId = $book->id;

    //     $fileName = 'book_cover_' . $book->id . '.jpg';
    //     Storage::put('public/' . $fileName, '');

    //     $this->deleteJson("/api/books/{$bookId}", [], ['Authorization' => 'Bearer ' . $token])
    //         ->assertStatus(204);

    //     $this->assertNull(Book::find($bookId));
    //     $this->assertFalse(Storage::exists('public/' . $fileName));
    // }
}
