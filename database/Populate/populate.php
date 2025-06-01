<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\AccountRulePopulate;
use Database\Populate\PetPopulate;
use Database\Populate\UserPopulate;
use Database\Populate\UserRulePopulate;

Database::create();
Database::migrate();
UserPopulate::populate();
UserRulePopulate::populate();
AccountRulePopulate::populate();
PetPopulate::populate();
