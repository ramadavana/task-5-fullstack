<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = new Post;
        $post->title = $request->title;
        $post->body = $request->body;
        $post->user_id = Auth::id();
        $post->category_id = $request->category_id;
        $post->save();

        return response()->json(['data' => $post], 201);
    }

    public function list()
    {
        $posts = Post::with('user', 'category')->get();

        return response()->json(['data' => $posts]);
    }

    public function show($id)
    {
        $post = Post::with('user', 'category')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(['data' => $post]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'body' => 'string',
            'category_id' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to update this post'], 403);
        }

        $post->title = $request->title ?? $post->title;
        $post->body = $request->body ?? $post->body;
        $post->category_id = $request->category_id ?? $post->category_id;
        $post->save();

        return response()->json(['data' => $post]);
    }

    public function delete($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'You are not authorized to delete this post'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
    public function index(Request $request)
    {
        $posts = Post::with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
        $perPage = $request->query('limit', 10);
        $posts = Post::with('category')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }
}
