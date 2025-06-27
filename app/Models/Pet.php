<?php

namespace App\Models;

use Core\Constants\Constants;
use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Core\Debug\Debugger;
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
 * @property string $image_name;
 * @property string $image_size;
 * @property string $image_temp_name;
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

    public const int MAX_IMAGE_ACEPTED_SIZE = (2 * 1048576);// 2MB
    public string $image_name;
    public int $image_size;
    public string $image_temp_name;

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
        if (isset($this->birth_date) && strlen($this->birth_date) !== 0) {
            Validations::isDate('birth_date', $this);
        } else {
            $this->birth_date = null;
        }
        if (isset($this->photo_url)) {
            Validations::inRangeLength('photo_url', 10, 255, $this);
        } else {
            $this->photo_url = null;
        }
        if (isset($this->created_at)) {
            Validations::isDate('created_at', $this);
        } else {
            $this->created_at = null;
        }
        if (isset($this->updated_at)) {
            Validations::isDate('updated_at', $this);
        } else {
            $this->updated_at = null;
        }

        if (isset($this->weight)) {
            Validations::match('weight', '/^(-)?[0-9]{1,5}(\.[0-9]{1,2})?$/', $this);
            Validations::isFloat('weight', $this);
        } else {
            $this->weight = null;
        }

        if (isset($this->image_name) && $this->image_name !== '') {
            Validations::match('image_name', '/^.*\.(jpeg|jpg|png)$/', $this);
            Validations::inRange('image_size', 1, self::MAX_IMAGE_ACEPTED_SIZE, $this);
        } else {
            $this->image_name = '';
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
        if (isset($this->image_name) && $this->image_name !== '' && $this->isValid()) {
            if (isset($this->photo_url)) {
                unlink(Constants::rootPath()->join('public/assets/uploads/' . $this->photo_url));
            }
            $tokens = explode('.', $this->image_name);
            $this->photo_url = md5(uniqid()) . '.' . array_pop($tokens);
            move_uploaded_file($this->image_temp_name, Constants::rootPath()->join('public/assets/uploads/' . $this->photo_url));
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
        if (isset($this->photo_url)) {
            unlink(Constants::rootPath()->join('public/assets/uploads/' . $this->photo_url));
        }
        return $this->update(['active' => '0', 'photo_url' => null]);
    }

    public function activate(): bool
    {
        return $this->update(['active' => '1']);
    }

    public function update(array $data): bool
    {
        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
        $this->updated_at = gmdate('Y-m-d');
        return $this->save();
    }
}
