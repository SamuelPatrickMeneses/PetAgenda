<?php

namespace App\Controllers;

use App\Models\Pet;
use Core\Debug\Debugger;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class PetController extends Controller
{
    public function index(Request $req): void
    {
        $title = 'Seus Pets';
        $page = $req->getParam('page');
        $user = Auth::user();
        $paginator =  Pet::findActivesByUserId($user->id, $page ?? 1);
        $this->render('pet/index', compact('title', 'paginator'));
    }
    public function newForm(): void
    {
        $title = 'new Pet';
        $this->render('pet/new', compact('title'));
    }
    public function create(Request $req): void
    {
        $user = Auth::user();
        $pet = new Pet([
          'user_id' => $user->id,
          'name' => $req->getParam('name'),
          'description' => $req->getParam('description'),
          'breed' => $req->getParam('breed'),
          'specie' => $req->getParam('specie'),
          'birth_date' => $req->getParam('birth_date'),
          'weight' => $req->getParam('weight'),
        ]);
        $image = $_FILES['image'];
        if (isset($image) && isset($image['name'])) {
            $pet->image_name = $image['name'];
            $pet->image_temp_name = $image['tmp_name'];
            $pet->image_size = $image['size'];
            $pet->image_type = $image['type'];
        }
        if ($pet->save()) {
            FlashMessage::success('success');
            $this->redirectTo(route('user.pets.view'));
        } else {
            foreach ($pet->getErrors() as $error) {
                if ($error === "Don't math the patern /^.*\.(jpeg|jpg|png)$/") {
                    FlashMessage::danger('Nome ou formato de imagem invalido!');
                } else {
                    FlashMessage::danger($error);
                }
            }
            $this->redirectTo(route('user.pets.create'));
        }
    }

    public function delete(Request $req): void
    {
        $user = Auth::user();
        $pet_id = intval($req->getParam('id'));
        $pet = Pet::findById($pet_id);
        if (isset($pet) && $user->id === $pet->user_id) {
            $pet->unactivate();
            FlashMessage::success('delete successfully');
            $this->redirectTo(route('user.pets.view'));
        }
        $this->redirectTo(route('user.pets.delete'));
    }
    public function edit(Request $req): void
    {
        $title = 'edit pet';
        $user = Auth::user();
        $pet = Pet::findById(intval($req->getParam('id')));
        if (isset($pet) && $pet->user_id === $user->id) {
            FlashMessage::success('success');
            $this->render('pet/edit', compact('title', 'pet'));
        } else {
            FlashMessage::danger('não altorisado');
            $this->redirectTo(route('user.pets.view'));
        }
    }
    public function update(Request $req): void
    {
        $columns = [
        'name',
        'specie',
        'breed',
        'description',
        'weight',
        'birth_date',
        ];
        if (intval($req->getParam('id'))) {
            $param = [];
            foreach ($columns as $col) {
                $temp = $req->getParam($col);
                if (isset($temp)) {
                    $param[$col] = $req->getParam($col);
                }
            }
            $pet = Pet::findById($req->getParam('id'));
            if ($pet !== null && $pet->user_id === Auth::user()->id) {
                $image = $_FILES['image'];
                if (isset($image) && isset($image['name'])) {
                    $pet->image_name = $image['name'];
                    $pet->image_temp_name = $image['tmp_name'];
                    $pet->image_size = $image['size'];
                    $pet->image_type = $image['type'];
                }

                if ($pet->update($param)) {
                    FlashMessage::success('update with success');
                    $this->redirectTo(route('user.pets.view'));
                } else {
                    foreach ($pet->getErrors() as $error) {
                        if ($error === "Don't math the patern /^.*\.(jpeg|jpg|png)$/") {
                            FlashMessage::danger('Nome ou formato de imagem invalido!');
                        } else {
                            FlashMessage::danger($error);
                        }
                    }
                    $title = 'Seus Pets';
                    $this->render('pet/edit', compact('pet', 'title'));
                }
                $title = 'Seus Pets';
                $this->render('pet/edit', compact('pet', 'title'));
            } else {
                FlashMessage::danger('não altorisado');
                $this->redirectTo(route('user.pets.view'));
            }
        }
    }
}
