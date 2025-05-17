<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $phone
 * @property string $encrypted_password
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = [
      'phone',
      'encrypted_password',
      'is_active',
      'last_login',
      'created_at',
      'updated_at'
    ];

    protected array $rules;
    protected ?string $password = null;
    protected ?string $password_confirmation = null;
    public function validates(): void
    {
        Validations::notEmpty('phone', $this);
        Validations::uniqueness('phone', $this);
        Validations::match('phone', '/^[0-9]{11}$/', $this);

        Validations::isPasswordStrong($this);
        if ($this->newRecord()) {
            if (Validations::passwordConfirmation($this)) {
                $this->encrypted_password  = $this->password;
            }
        }
    }

    public function validateLogin(): bool
    {
        Validations::notEmpty('phone', $this);
        Validations::notEmpty('password', $this);
        Validations::match('phone', '/^[0-9]{11}$/', $this);
        return $this->isValid();
    }

    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password == null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }

    public static function findByPhone(string $phone): User | null
    {
        return User::findBy(['phone' => $phone]);
    }

    public function grant(string $rule)
    {
        $rule = UserRule::findByRuleType($rule);
        if (isset($rule) && $this->id) {
            $grant = new AccountRule([
            'rule_id' => $rule->id,
            'user_id' => $this->id
            ]);
            $grant->save();
            return true;
        }
        return false;
    }

    public function __set(string $property, mixed $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'encrypted_password' &&
            $this->newRecord() &&
            $value !== null && $value !== ''
        ) {
            parent::__set('encrypted_password', password_hash($value, PASSWORD_DEFAULT));
        }
    }

    public function hasRule(string $rule)
    {
        if (!isset($this->rules)) {
            $btm = $this->BelongsToMany(
                UserRule::class,
                'account_rules',
                'user_id',
                'rule_id'
            );
            $this->rules = array_map(function ($obj) {
                return $obj->rule_type;
            }, $btm->get());
        }
        return in_array($rule, $this->rules);
    }
}
