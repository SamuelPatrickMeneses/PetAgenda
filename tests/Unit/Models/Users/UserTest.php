<?php

namespace Tests\Unit\Models\Users;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;
    private User $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User([
            'phone' => '00000000001',
            'password' => '123456aA',
            'password_confirmation' => '123456aA'
        ]);
        $this->user->save();

        $this->user2 = new User([
            'phone' => '00000000002',
            'password' => '123456bB',
            'password_confirmation' => '123456bB'
        ]);
        $this->user2->save();
    }

    public function test_should_create_new_user(): void
    {
        $this->assertCount(2, User::all());
    }

    public function test_all_should_return_all_users(): void
    {
        $this->user2->save();

        $users[] = $this->user->id;
        $users[] = $this->user2->id;

        $all = array_map(fn($user) => $user->id, User::all());

        $this->assertCount(2, $all);
        $this->assertEquals($users, $all);
    }

    public function test_destroy_should_remove_the_user(): void
    {
        $this->user->destroy();
        $this->assertCount(1, User::all());
    }

    public function test_set_id(): void
    {
        $this->user->id = 10;
        $this->assertEquals(10, $this->user->id);
    }

    public function test_set_phone(): void
    {
        $this->user->phone = '00000000001';
        $this->assertEquals('00000000001', $this->user->phone);
    }


    public function test_errors_should_return_errors(): void
    {
        $user = new User();

        $this->assertFalse($user->isValid());
        $this->assertFalse($user->save());
        $this->assertTrue($user->hasErrors());

        $this->assertEquals('Deve ser uma senha forse!', $user->errors('encrypted_password'));
        $this->assertEquals("Don't math the patern /^[0-9]{11}$/", $user->errors('phone'));
    }

    public function test_errors_should_return_password_confirmation_error(): void
    {
        $user = new User([
            'phone' => '00000000001',
            'password' => '123456',
            'password_confirmation' => '1234567'
        ]);

        $this->assertFalse($user->isValid());
        $this->assertFalse($user->save());

        $this->assertEquals('as senhas devem ser idênticas!', $user->errors('password'));
    }

    public function test_find_by_id_should_return_the_user(): void
    {
        $this->assertEquals($this->user->id, User::findById($this->user->id)->id);
    }

    public function test_find_by_id_should_return_null(): void
    {
        $this->assertNull(User::findById(3));
    }

    public function test_find_by_phone_should_return_the_user(): void
    {
        $this->assertEquals($this->user->id, User::findByPhone($this->user->phone)->id);
    }

    public function test_find_by_phone_should_return_null(): void
    {
        $this->assertNull(User::findByPhone('not.exits@example.com'));
    }

    public function test_authenticate_should_return_the_true(): void
    {
        $this->assertTrue($this->user->authenticate('123456aA'));
    }

    public function test_authenticate_should_return_false(): void
    {
        $this->assertFalse($this->user->authenticate(''));
        $this->assertFalse($this->user->authenticate('wrong'));
    }

    public function test_update_should_not_change_the_password(): void
    {
        $this->user->password = '654321';
        $this->user->save();

        $this->assertTrue($this->user->authenticate('123456aA'));
        $this->assertFalse($this->user->authenticate('654321'));
    }
}
