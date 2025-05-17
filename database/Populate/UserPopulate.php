<?php

namespace Database\Populate;

use App\Models\User;

class UserPopulate
{
    public static function populate(): void
    {

        $numberOfResgisters = 4;
        $basePassword = 'SenhaSenha';
        $basePhone = '0000000000';

        for ($i = 0; $i < $numberOfResgisters; $i++) {
          $password = $basePassword . strval($i);
          $user = new User([
              'password' =>  $password,
              'password_confirmation' => $password,
              'phone' => $basePhone . strval($i)
          ]);
          $user->save();
        }

        echo "users populate with $numberOfResgisters\n";
    }
}
