<?php

namespace Tests\Acceptance\User;

use App\Models\Pet;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class PetAcceptanceCest extends BaseAcceptanceCest
{
    public function upImage(AcceptanceTester $page): void
    {
        $page->amOnPage('/logout');
        $page->login('00000000003', 'SenhaSenha3');
        $page->see('Home');
        $page->amOnPage('/my/pets#pitokinho');
        $page->see('Seus pets');
        $page->click('#edit-1');
        $page->attachFile('#image', 'avatar_test.jpg');
        $page->click('input[value="Save"]');
        $page->amOnPage('/my/pets');
        $url = '/assets/uploads/' . (Pet::findById(1)->photo_url ?? '');
        $page->seeElement('img[src="' . $url . '"]');
    }
}
