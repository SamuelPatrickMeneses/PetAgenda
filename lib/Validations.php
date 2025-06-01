<?php

namespace Lib;

use Core\Database\Database;

class Validations
{
    public static function notEmpty($attribute, $obj)
    {
        if ($obj->$attribute === null || $obj->$attribute === '') {
            $obj->addError($attribute, 'não pode ser vazio!');
            return false;
        }

        return true;
    }
    public static function isEmail($attribute, $obj)
    {
        if (!filter_var($obj->$attribute, FILTER_VALIDATE_EMAIL)) {
            $obj->addError($attribute, 'Deve ser um email valido!');
            return false;
        }

        return true;
    }

    public static function inEnum(string $attribute, array $enum, $obj): bool
    {
        if (!in_array($obj->$attribute, $enum)) {
            $obj->addError($attribute, 'Campo invalida!');
            return false;
        }
        return true;
    }

    public static function isString(string $attribute, $obj): bool
    {
        if (!is_string($obj->$attribute)) {
            $obj->addError($attribute, 'Deve conter um texto!');
            return false;
        }
        return true;
    }

    public static function isInt(string $attribute, $obj): bool
    {
        if (!is_numeric($obj->$attribute) || !is_int(intval($obj->$attribute))) {
            $obj->addError($attribute, 'Deve conter um numero inteiro!');
            return false;
        }
        return true;
    }

    public static function isFloat(string $attribute, $obj): bool
    {
        if (!is_numeric($obj->$attribute) || !is_float(floatval($obj->$attribute))) {
            $obj->addError($attribute, 'Deve conter um numero real!');
            return false;
        }
        return true;
    }

    public static function inRange(string $attribute, int | float $min, int | float $max, $obj): bool
    {
        $val = floatval($obj->$attribute);
        if ($val >= $min) {
            $obj->addError($attribute, "Deve ser maior ou igual à $min!");
            return false;
        }
        if ($val <= $max) {
            $obj->addError($attribute, "Deve ser menor ou igual à $max!");
            return false;
        }
        return true;
    }

    public static function inRangeLength(string $attribute, int | float $min, int | float $max, $obj): bool
    {
        $val = strlen($obj->$attribute);
        if ($val < $min) {
            $obj->addError($attribute, "Deve ser maior ou igual à $min caracteres!");
            return false;
        }
        if ($val > $max) {
            $obj->addError($attribute, "Deve ser menor ou igual à $max caracteres!");
            return false;
        }
        return true;
    }

    public static function isPasswordStrong($obj)
    {
        if (
            preg_match('/[A-Z]/', $obj->password) &&
            preg_match('/[a-z]/', $obj->password) &&
            preg_match('/[0-9]/', $obj->password) &&
            strlen($obj->password) >= 8
        ) {
            return true;
        }
        $obj->addError('encrypted_password', 'Deve ser uma senha forse!');
        return false;
    }

    public static function match(string $field, string $patern, $obj)
    {
        if (!preg_match($patern, $obj->$field)) {
            $obj->addError($field, "Don't math the patern $patern");
            return false;
        }
        return true;
    }

    public static function passwordConfirmation($obj)
    {
        if ($obj->password !== $obj->password_confirmation) {
            $obj->addError('password', 'as senhas devem ser idênticas!');
            return false;
        }

        return true;
    }

    public static function uniqueness($fields, $object)
    {
        $dbFieldsValues = [];
        $objFieldValues = [];

        if (!is_array($fields)) {
            $fields = [$fields];
        }

        if (!$object->newRecord()) {
            $dbObject = $object::findById($object->id);

            foreach ($fields as $field) {
                $dbFieldsValues[] = $dbObject->$field;
                $objFieldValues[] = $object->$field;
            }

            if (
                !empty($dbFieldsValues) &&
                !empty($objFieldValues) &&
                $dbFieldsValues === $objFieldValues
            ) {
                return true;
            }
        }

        $table = $object::table();
        $conditions = implode(' AND ', array_map(fn($field) => "{$field} = :{$field}", $fields));

        $sql = <<<SQL
            SELECT id FROM {$table} WHERE {$conditions};
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);

        foreach ($fields as $field) {
            $stmt->bindValue($field, $object->$field);
        }

        $stmt->execute();

        if ($stmt->rowCount() !== 0) {
            foreach ($fields as $field) {
                $object->addError($field, 'já existe um registro com esse dado');
            }
            return false;
        }

        return true;
    }
    public static function isIdFrom($field, $obj, $related)
    {
        $entity = $related::findById($obj->$field);
        return isset($entity) && $entity->id === $obj->$field;
    }
}
