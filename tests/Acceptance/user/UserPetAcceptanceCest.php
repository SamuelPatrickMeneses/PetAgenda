<?php

namespace Tests\Acceptance\User;

use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class UserPetAcceptanceCest extends BaseAcceptanceCest
{
    public function loginAndGoToPetView(AcceptanceTester $page): void
    {
        $page->amOnPage('/logout');
        $page->login('00000000003', 'SenhaSenha3');
        $page->see('Home');
        $page->amOnPage('/my/pets#pitokinho');
        $page->see('Seus pets');
        $page->see('pitokinho', '#pitokinho');
    }
    public function deletePet(AcceptanceTester $page): void
    {
        $page->amOnPage('/logout');
        $page->login('00000000003', 'SenhaSenha3');
        $page->see('Home');
        $page->amOnPage('/my/pets#pitokinho');
        $page->see('Seus pets');
        $page->click('#delete-1');
        $page->see('delete successfully');
    }
    public function editPet(AcceptanceTester $page): void
    {
        $page->amOnPage('/logout');
        $page->login('00000000003', 'SenhaSenha3');
        $page->see('Home');
        $page->amOnPage('/my/pets#pitokinho');
        $page->see('Seus pets');
        $page->click('#edit-1');
        $page->fillField('#name', 'nicolal');
        $page->click("Save");
        $page->see('update with success');
        $page->see('nicolal');
    }
}
