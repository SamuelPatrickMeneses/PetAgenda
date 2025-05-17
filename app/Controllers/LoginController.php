<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class LoginController extends Controller
{
  
    public function index(): void
    {
        $title = 'login';
        $this->render('authentications/login', compact('title'));
    }
    public function authenticate(Request $request): void
    {
        $params = $request->getParam('user');
        $user = User::findBy(['phone' => $params['phone']]);
        if ($user && $user->authenticate($params['password'])) {
          Auth::login($user);
          FlashMessage::success('Login realizado com sucesso!');

          // Verificar tipo de usuário
          $this->redirectTo(route('root'));
        } else {
            FlashMessage::danger('telefone e/ou senha inválidos!');
            $this->redirectTo(route('login.view'));
        }
    }
    public function destroy(): void
    {
        Auth::logout();
        FlashMessage::success('Logout realizado com sucesso!');
        $this->redirectTo(route('login.view'));
    }
}
