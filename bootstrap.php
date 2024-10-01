<?php
require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
   "driver" => "mysql",
   "host" =>"localhost",
   "database" => "mobipower",
   "username" => "mobipower",
   "password" => "mobipower"
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();