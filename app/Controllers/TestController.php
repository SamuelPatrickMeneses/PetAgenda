<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;

class TestController extends Controller
{
    public function client(): void
    {
        $title = 'Client Space';
        $this->render('test/client_space', compact('title'));
    }
    public function admin(): void
    {
        $title = 'Admin Space';
        $this->render('test/admin_space', compact('title'));
    }
}
