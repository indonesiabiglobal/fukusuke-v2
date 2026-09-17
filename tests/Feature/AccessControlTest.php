<?php

namespace Tests\Feature;

use App\Models\Access;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function makeUserWithAccessCode(string $code): User
    {
        $user = User::factory()->create(['status' => 1]);
        $role = Role::create(['role_name' => 'Test Role ' . uniqid(), 'description' => 'test', 'status' => 1, 'can_delete' => 1]);
        $access = new Access();
        $access->access_name = 'Test Access ' . uniqid();
        $access->code = $code;
        $access->description = 'test';
        $access->status = 1;
        $access->save();
        $role->access()->attach($access->id);
        $user->roles()->attach($role->id);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/security-management');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_without_code_gets_403(): void
    {
        $user = $this->makeUserWithAccessCode('SOME-OTHER-CODE');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(403);
    }

    public function test_authenticated_user_with_matching_code_gets_200(): void
    {
        $user = $this->makeUserWithAccessCode('ADM');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(200);
    }

    public function test_wildcard_code_bypasses_every_check(): void
    {
        $user = $this->makeUserWithAccessCode('*');

        $response = $this->actingAs($user)->get('/security-management');

        $response->assertStatus(200);
    }
}
