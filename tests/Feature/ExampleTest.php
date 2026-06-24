<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_see_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('auth.user.email', $user->email)
                ->where('auth.user.profile', $user->profile)
            );
    }

    public function test_authenticated_users_can_see_their_profile(): void
    {
        $user = User::factory()->create([
            'profile' => 'administrador',
        ]);

        $response = $this->actingAs($user)->get('/meu-perfil');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Show')
                ->where('auth.user.email', $user->email)
                ->where('auth.user.profile', 'administrador')
            );
    }
}
