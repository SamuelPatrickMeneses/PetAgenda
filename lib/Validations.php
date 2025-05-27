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

    public static function isPasswordStrong($obj)
    {
        if (
            preg_match('/[A-Z]/', $obj->password) &&
            preg_match('/[a-z]/', $obj->password) &&
            preg_match('/[0-9]/', $obj->password)
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
