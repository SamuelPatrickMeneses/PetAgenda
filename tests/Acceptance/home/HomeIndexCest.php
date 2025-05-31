<?php

namespace Tests\Acceptance\home;

use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class HomeIndexCest extends BaseAcceptanceCest
{
    public function loginAndGoToRome(AcceptanceTester $page): void
    {
        $page->amOnPage('/');
        $page->see('Login', '//h1');
        $page->fillField('#user_phone', '00000000001');
        $page->fillField('#user_password', 'SenhaSenha1');
        $page->click('input[type="submit"].btn');
        $page->see('Home');
    }
}
