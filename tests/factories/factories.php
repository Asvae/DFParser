<?php

$factory('Laravel_test\User', [
    'name'=>$faker->name,
    'email'=>$faker->email,
    'password'=>bcrypt('secret'),
]);

$factory('Comment', [
    'user_id' => '5',
    'body' => $faker->sentence,
]);

