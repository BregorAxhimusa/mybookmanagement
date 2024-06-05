<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
/**
 * @OA\Info(
 *      title="Book API",
 *      version="1.0.0",
 *      description="API endpoints to manage books",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      ),
 *      @OA\License(
 *          name="MIT License",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 */
class BookController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/books",
 *     summary="Get a list of books",
 *     tags={"Books"},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of items per page",
 *         @OA\Schema(
 *             type="integer",
 *             default=10
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $sortField = $request->query('sort_field', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');

        $allowedFields = ['id', 'title', 'author', 'publication_year', 'created_at', 'updated_at'];
        $sortField = in_array($sortField, $allowedFields) ? $sortField : 'id';
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        $books = Book::orderBy($sortField, $sortDirection)->paginate($perPage);

        return response()->json($books, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     tags={"Books"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author", "publication_year"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="publication_year", type="integer", format="int32"),
     *             @OA\Property(property="cover_image_path", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'cover_image_path' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image_path')) {
            $image = $request->file('cover_image_path');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $validatedData['cover_image_path'] = '/images/' . $imageName;
        }

        $book = Book::create($validatedData);

        return response()->json($book, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/books/{book}",
     *     summary="Update an existing book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="ID of the book to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="publication_year", type="integer", format="int32"),
     *             @OA\Property(property="cover_image_path", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'publication_year' => 'sometimes|required|integer|min:1000|max:' . (date('Y') + 1),
            'cover_image_path' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image_path')) {
            $image = $request->file('cover_image_path');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $validatedData['cover_image_path'] = '/images/' . $imageName;
        }

        $book->update($validatedData);

        return response()->json($book, 200);
    }

        /**
     * @OA\Get(
     *     path="/api/books/{book}",
     *     summary="Get details of a specific book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="ID of the book",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book details retrieved successfully"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Book $book)
    {
        return response()->json($book, 200);
    }

       /**
     * @OA\Delete(
     *     path="/api/books/{book}",
     *     summary="Delete a specific book",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         description="ID of the book",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Book deleted successfully"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}
