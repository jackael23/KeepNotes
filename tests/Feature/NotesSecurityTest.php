<?php

namespace Tests\Feature;

use App\Models\KeepNotes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function noteFor(User $user, array $attrs = []): KeepNotes
    {
        return $user->notes()->create(array_merge([
            'title' => 'Note of '.$user->name,
            'content' => '<p>secret</p>',
        ], $attrs));
    }

    public function test_guests_cannot_use_the_notes_api(): void
    {
        $this->getJson('/api/get_notes')->assertUnauthorized();
        $this->getJson('/api/getDepartment')->assertUnauthorized();
        $this->postJson('/api/add_note', ['title' => 'x', 'content' => 'y'])->assertUnauthorized();
        $this->postJson('/api/edit_note', ['id' => 1, 'title' => 'x'])->assertUnauthorized();
        $this->postJson('/api/edit_action', ['id' => 1])->assertUnauthorized();
    }

    public function test_plain_text_password_lookup_route_is_gone(): void
    {
        // No POST route exists any more (404 or 405 depending on the SPA catch-all GET route).
        $status = $this->postJson('/api/getUser', ['username' => 'a', 'password' => 'b'])->status();
        $this->assertContains($status, [404, 405]);
    }

    public function test_users_only_see_their_own_notes(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $this->noteFor($alice, ['title' => 'Alice note']);
        $this->noteFor($bob, ['title' => 'Bob note']);

        $this->actingAs($alice)->getJson('/api/get_notes')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.title', 'Alice note');

        $this->actingAs($alice)->getJson('/api/getDepartment')
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_new_notes_belong_to_the_signed_in_user(): void
    {
        $alice = User::factory()->create();

        $this->actingAs($alice)->postJson('/api/add_note', ['title' => 'Groceries', 'content' => '<p>eggs</p>'])
            ->assertCreated()
            ->assertJsonPath('title', 'Groceries');

        $this->assertDatabaseHas('keep_notes', ['title' => 'Groceries', 'user_id' => $alice->id]);
    }

    public function test_users_cannot_edit_or_trash_someone_elses_note(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $bobNote = $this->noteFor($bob, ['title' => 'Bob note']);

        $this->actingAs($alice)->postJson('/api/edit_note', ['id' => $bobNote->id, 'title' => 'hacked', 'content' => 'x'])
            ->assertNotFound();
        $this->actingAs($alice)->postJson('/api/edit_action', ['id' => $bobNote->id, 'trash' => now()->toDateTimeString()])
            ->assertNotFound();

        $this->assertDatabaseHas('keep_notes', ['id' => $bobNote->id, 'title' => 'Bob note', 'trash' => null]);
    }

    public function test_owner_can_edit_and_move_their_note(): void
    {
        $alice = User::factory()->create();
        $note = $this->noteFor($alice);

        $this->actingAs($alice)->postJson('/api/edit_note', ['id' => $note->id, 'title' => 'Updated', 'content' => '<p>new</p>'])
            ->assertOk()->assertJsonPath('title', 'Updated');

        $this->actingAs($alice)->postJson('/api/edit_action', [
            'id' => $note->id,
            'reminder' => '2026-10-20 09:00:00',
            'archive' => null,
            'trash' => null,
            'deleted_at' => null,
        ])->assertOk();

        $this->assertDatabaseHas('keep_notes', ['id' => $note->id, 'title' => 'Updated', 'reminder' => '2026-10-20 09:00:00']);
    }

    public function test_invalid_input_returns_validation_errors(): void
    {
        $alice = User::factory()->create();

        $this->actingAs($alice)->postJson('/api/add_note', ['content' => 'no title'])
            ->assertStatus(422)->assertJsonValidationErrors('title');

        $this->actingAs($alice)->postJson('/api/edit_action', ['id' => 999, 'reminder' => 'not a date'])
            ->assertStatus(422)->assertJsonValidationErrors('reminder');
    }

    public function test_long_notes_are_saved_in_full(): void
    {
        $alice = User::factory()->create();
        $long = '<p>'.str_repeat('a', 5000).'</p>';

        $this->actingAs($alice)->postJson('/api/add_note', ['title' => 'Long', 'content' => $long])->assertCreated();

        $this->assertSame($long, KeepNotes::where('title', 'Long')->value('content'));
    }
}
