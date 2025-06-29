<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\User;
use Core\Constants\Constants;
use Tests\TestCase;

class PetTest extends TestCase
{
    private Pet $pet1;
    private Pet $pet2;
    public function setUp(): void
    {
        parent::setUp();
        // Configuração inicial para cada teste
        $user = new User([
          'phone' => '00000000001',
          'password' => '123456aA',
          'password_confirmation' => '123456aA'
        ]);
        $user->save();


        $this->pet1 = new Pet([
        'user_id' => 1,
        'name' => 'pitokinho',
        'specie' => 'cachorro',
        'breed' => 'viralata',
        'weight' => '5.0',
        ]);
        $this->pet1->save();

        $this->pet2 = new Pet([
        'user_id' => 1,
        'name' => 'galileu',
        'specie' => 'galo',
        'description' => 'adotado da rua, arisco e asmatico.',
        'weight' => '2.5',
        ]);
        $this->pet2->save();
    }

    public function testUpload(): void
    {
        $originalPath = Constants::rootPath()->join('tests/Support/Data/avatar_test.jpg');
        $tempFile = tempnam(sys_get_temp_dir(), 'img_test_');
        $tempFileWithExtension = $tempFile . '.jpg';
        rename($tempFile, $tempFileWithExtension);
        copy($originalPath, $tempFileWithExtension);
        $this->pet1->image_temp_name = $tempFileWithExtension;
        $this->pet1->image_name = 'avatar_test.jpg';
        $this->pet1->image_size = 1024;
        $this->pet1->image_type = 'image/jpeg';
        $this->assertTrue($this->pet1->save());
        $this->assertTrue(file_exists('public/assets/uploads/' . $this->pet1->photo_url));
        unlink(Constants::rootPath()->join('public/assets/uploads/' . $this->pet1->photo_url));
    }

    public function testInvalidImegeSize(): void
    {
        $originalPath = Constants::rootPath()->join('tests/Support/Data/avatar_test.jpg');
        $tempFile = tempnam(sys_get_temp_dir(), 'img_test_');
        $tempFileWithExtension = $tempFile . '.jpg';
        rename($tempFile, $tempFileWithExtension);
        copy($originalPath, $tempFileWithExtension);
        $this->pet1->image_temp_name = $tempFileWithExtension;
        $this->pet1->image_name = 'avatar_test.jpg';
        $this->pet1->image_size = 0;
        $this->pet1->image_type = 'image/jpeg';
        $this->assertFalse($this->pet1->isValid());
        unlink($tempFileWithExtension);
    }

    public function testInvalidImegeName(): void
    {
        $originalPath = Constants::rootPath()->join('tests/Support/Data/avatar_test.jpg');
        $tempFile = tempnam(sys_get_temp_dir(), 'img_test_');
        $tempFileWithExtension = $tempFile . '.jpg';
        rename($tempFile, $tempFileWithExtension);
        copy($originalPath, $tempFileWithExtension);
        $this->pet1->image_temp_name = $tempFileWithExtension;
        $this->pet1->image_name = 'avatar_test.txt';
        $this->pet1->image_size = 1024;
        $this->pet1->image_type = 'image/jpeg';
        $this->assertFalse($this->pet1->isValid());
        unlink($tempFileWithExtension);
    }

    public function testChekBreed(): void
    {
        $this->assertEquals('Campo invalida!', $this->pet2->errors('specie'));
    }

    public function testChekValid(): void
    {

        $this->assertTrue($this->pet1->isValid());
        $this->assertFalse($this->pet2->isValid());
    }
    public function testSoftDelete(): void
    {
        $this->pet1->unactivate();
        $pets = Pet::findActivesByUserId(1)->registers();
        $this->assertEmpty($pets);
    }
    public function testFindAll(): void
    {
        $this->pet1->unactivate();
        $pets = Pet::findByUserId(1)->registers();
        $this->assertEquals(1, count($pets));
    }
    public function testUpdat(): void
    {

        $this->pet1->update([
        'name' => 'osvaldo'
        ]);
        $pet = Pet::findById(1);
        $this->assertEquals('osvaldo', $pet->name);
    }
}
