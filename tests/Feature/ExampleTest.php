<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Http\Request;

use App\Models\Post;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_post(Request $faker)
    {
        $data = [
            'title' => $this->$faker->sentence,
            'body' => $this->$faker->paragraph
        ];

        $response = $this->post('/api/v1/posts', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', $data);
    }

    public function test_can_list_posts()
    {
        $posts = Post::factory()->count(5)->create();

        $response = $this->get('/api/v1/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'body', 'category_id', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    public function test_can_show_post()
    {
        $post = Post::factory()->create();

        $response = $this->get("/api/v1/posts/$post->id");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $post->id,
                     'title' => $post->title,
                     'body' => $post->body,
                     'category_id' => $post->category_id
                 ]);
    }

    public function test_can_update_post(Request $faker)
    {
        $post = Post::factory()->create();

        $data = [
            'title' => $this->$faker->sentence,
            'body' => $this->$faker->paragraph
        ];

        $response = $this->put("/api/v1/posts/$post->id", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $data);
    }

    public function test_can_delete_post()
    {
        $post = Post::factory()->create();

        $response = $this->delete("/api/v1/posts/$post->id");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}