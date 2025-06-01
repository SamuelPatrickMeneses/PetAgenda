<?php
namespace Database\Populate;

use App\Models\Pet;

class PetPopulate
{
  public static function populate(): void
  {
    $numberOfResgisters = 2;
    $pet1 = new Pet([
      'user_id' => '3',
      'name' => 'pitokinho',
      'specie' => 'cachorro',
      'breed' => 'viralata',
      'weight' => '5.0',
    ]); 
    $pet1->save();
    $pet2 = new Pet([
      'user_id' => '3',
      'name' => 'galileu',
      'specie' => 'gato',
      'description' => 'adotado da rua, arisco e asmatico.',
      'weight' => '2.5',
    ]); 
    $pet2->save();
    echo "users populate with $numberOfResgisters\n";
  }
}
