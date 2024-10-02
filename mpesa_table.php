<?php

include_once dirname(__FILE__) . '/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;
Capsule::schema()->create('transactions', function ($table) {
       $table->increments('id');
       $table->string('checkout_id');
       $table->string('amount');
       $table->string('meter');
       $table->string('phone');
       $table->integer('status')->default(0); // 1 - completed, 0 - not checked, 2 - failed
       $table->timestamps();
   }); 
