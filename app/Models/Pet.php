<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Lib\Paginator;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $specie
 * @property string|null $breed
 * @property string|null $description
 * @property float|null $weight
 * @property string|null $birth_date
 * @property string|null $photo_url
 * @property string | null $created_at
 * @property string | null $updated_at
 * @property string $active
 */
class Pet extends Model
{
    protected static string $table = 'pets';
    protected static array $columns = [
      'user_id',
      'name',
      'specie',
      'breed',
      'description',
      'weight',
      'birth_date',
      'photo_url',
      'created_at',
      'updated_at',
      'active'
    ];
    /**
    * @var array<string>
    */
    public static array $species = ['cachorro','gato','ave','roedor','reptil','outro'];

    public function validates(): void
    {
        Validations::notEmpty('user_id', $this);
        Validations::notEmpty('name', $this);
        Validations::inRangeLength('name', 0, 50, $this);
        Validations::notEmpty('specie', $this);
        Validations::inEnum('specie', static::$species, $this);

        if (isset($this->breed)) {
            Validations::inRangeLength('breed', 0, 50, $this);
        } else {
            $this->breed = null;
        }
        if (isset($this->description)) {
            Validations::inRangeLength('description', 0, 140, $this);
        } else {
            $this->description = null;
        }
        if (isset($this->birth_date) && $this->birth_date !== '') {
            Validations::match('birth_date', '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this);
        } else {
            $this->birth_date = null;
        }
        if (isset($this->photo_url)) {
            Validations::inRangeLength('photo_url', 10, 255, $this);
        } else {
            $this->photo_url = null;
        }
        if (isset($this->created_at)) {
            Validations::match('created_at', '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this);
        } else {
            $this->created_at = null;
        }
        if (isset($this->updated_at)) {
            Validations::match('updated_at', '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this);
        } else {
            $this->updated_at = null;
        }

        if (isset($this->weight)) {
            Validations::match('weight', '/^(-)?[0-9]{1,5}(\.[0-9]{1,2})?$/', $this);
            Validations::isFloat('weight', $this);
        } else {
            $this->weight = null;
        }
    }

    public function save(): bool
    {
        if ($this->newRecord()) {
            $this->created_at = gmdate('Y-m-d');
            $this->active = '1';
        } else {
            $this->updated_at = gmdate('Y-m-d');
        }
        return parent::save();
    }

    public static function findActivesByUserId(int $id, int $page = 1, int $per_page = 10): Paginator
    {
        return static::paginate(
            page: $page,
            per_page: $per_page,
            route: 'user.pets.view',
            conditions: ['user_id' => $id, 'active' => '1']
        );
    }

    public static function findByUserId(int $id, int $page = 1, int $per_page = 10): Paginator
    {
        return static::paginate(
            page: $page,
            per_page: $per_page,
            conditions: ['user_id' => $id]
        );
    }

    public function unactivate(): bool
    {
        return $this->update(['active' => '0']);
    }

    public function activate(): bool
    {
        return $this->update(['active' => '1']);
    }

    public function update(array $data): bool
    {
        if (count($data)) {
            foreach ($data as $key => $val) {
                $this->$key = $val;
            }
            $this->updated_at = gmdate('Y-m-d');
            return $this->save();
        }
        return false;
    }
}
