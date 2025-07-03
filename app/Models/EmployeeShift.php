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
class EmployeeShift extends Model
{
    protected static string $table = 'employee_shifts';
    /** @var array<int, string> */
    protected static array $columns = [
      'auth_id',
      'day_of_week',
      'start_time',
      'end_time',
      'is_recurring'
    ];
    public const DAYS_OF_WEEK = [ 'seg','ter','qua','qui','sex','sab','dom' ];
    public function validates(): void
    {
      Validations::notEmpty('auth_id', $this);
      Validations::isInt('auth_id', $this);
      Validations::inRange('auth_id', 1, PHP_INT_MAX, $this);

      Validations::notEmpty('day_of_week', $this);
      Validations::isString('day_of_week', $this);
      Validations::inEnum('day_of_week', self::DAYS_OF_WEEK, $this);

      Validations::notEmpty('start_time', $this);
      Validations::isString('start_time', $this);
      Validations::match('start_time', '/^(2[0-3]|[0-1][0-9]):[0-5][0-9](:[0-5][0-9])?$/', $this);

      Validations::notEmpty('end_time', $this);
      Validations::isString('end_time', $this);
      Validations::match('end_time', '/^(2[0-3]|[0-1][0-9]):[0-5][0-9](:[0-5][0-9])?$/', $this);
      Validations::notEmpty('is_recurring', $this);
      Validations::isInt('is_recurring', $this);
      Validations::inRange('is_recurring', 0, 1, $this);
    }
}
