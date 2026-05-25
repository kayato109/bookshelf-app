<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_認証ユーザーがログインページにアクセスするとトップにリダイレクトされる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect('/');
    }

}
