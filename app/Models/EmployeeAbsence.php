<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;
use Lib\Validations;

/**
 * @property int $id
 * @property int $auth_id
 * @property string $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property string $is_recurring
 */
class EmployeeAbsence extends Model
{
    protected static string $table = 'employee_absences';
    /** @var array<int, string> */
    protected static array $columns = [
      'auth_id',
      'description',
      'start_date',
      'end_date',
      'reason'
    ];
    public const REASONS = ['ferias','licenca','treinamento','outro'];
    public function validates(): void
    {
        Validations::notEmpty('auth_id', $this);
        Validations::isInt('auth_id', $this);
        Validations::inRange('auth_id', 1, PHP_INT_MAX, $this);

        Validations::notEmpty('description', $this);
        Validations::isString('description', $this);

        Validations::notEmpty('start_date', $this);
        Validations::isString('start_date', $this);
        Validations::isDate('start_date', $this);

        Validations::notEmpty('end_date', $this);
        Validations::isString('end_date', $this);
        Validations::isDate('end_date', $this);

        Validations::notEmpty('reason', $this);
        Validations::isString('reason', $this);
        Validations::inEnum('reason', self::REASONS, $this);
    }
}
