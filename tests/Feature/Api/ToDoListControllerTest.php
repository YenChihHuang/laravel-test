<?php

namespace Tests\Feature\Api;

use App\Models\ToDoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ToDoListControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function indexSeeToDoLists()
    {
        $user = User::factory()->create();

        $count = 10;

        $toDoLists = ToDoList::factory()
            ->count($count)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user, 'api')->getJson(route('api.to_do_lists.index'));

        $toDoList = $toDoLists->random();

        $response->assertOk()
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment([
                'id'          => $toDoList->id,
                'title'       => $toDoList->title,
                'description' => $toDoList->description,
            ]);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function indexSeeEmptyToDoLists()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('api.to_do_lists.index'));

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function storeSuccess()
    {
        $user = User::factory()->create();

        $data = [
            'user_id'     => $user->id,
            'title'       => $this->faker->company,
            'description' => $this->faker->text(),
            'deadline_at' => now()->addDays(rand(3, 100))->toDateTimeString(),
        ];

        $response = $this->actingAs($user, 'api')->postJson(route('api.to_do_lists.store'), $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'title'       => data_get($data, 'title'),
                'description' => data_get($data, 'description'),
            ]);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function storeSeeValidationErrors()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title'   => $this->faker->company,
        ];

        $response = $this->actingAs($user, 'api')->postJson(route('api.to_do_lists.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function showToDoList()
    {
        $user = User::factory()->create();

        $toDoList = ToDoList::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user, 'api')->getJson(route('api.to_do_lists.show', $toDoList->id));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'title'       => $toDoList->title,
                'description' => $toDoList->description,
            ]);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function showNotFoundToDoList()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('api.to_do_lists.show', rand(100, 10000)));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function updateSuccess()
    {
        $user = User::factory()->create();

        $toDoList = ToDoList::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $data = [
            'user_id' => $user->id,
            'title'   => $this->faker->city,
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson(route('api.to_do_lists.update', $toDoList->id), $data);

        $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonFragment($data);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function updateNotFound()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title'   => $this->faker->city,
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson(route('api.to_do_lists.update', rand(100, 10000)), $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function deleteSuccess()
    {
        $user = User::factory()->create();

        $toDoList = ToDoList::factory()
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user, 'api')
            ->deleteJson(route('api.to_do_lists.destroy', $toDoList->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     * @group toDoList
     *
     * @return void
     */
    public function deleteNotFound()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson(route('api.to_do_lists.destroy', rand(100, 10000)));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
