<?php

namespace examples;
use src\Models\User;
use src\Connect;

require '../src/Config.php';
include '../src/Connect.php';
include '../src/CrudTrait.php';
include '../src/DataLayer.php';
include '../src/Models/User.php';

/** 
* SELECT STATMENTS
**/

// $user = (new User())->findById(19);
// var_dump($user);

// $users = (new User())->find()->fetch();
// var_dump($users);

// $user = (new User())->find()->fetch(true);
// var_dump($user);

// $users = (new User())->find("genre = :genre", "genre=m")->order("first_name DESC")->limit(3)->offset(1)->fetch(true);
// var_dump($users);

// $users = (new User())->find(null, null, "COUNT(genre) AS total, genre")->group("genre")->order("genre")->limit(3)->fetch(true);
// foreach ($users as $user) {
// 	var_dump("Sexo: " . $user->data()->genre);
// 	var_dump("TOTAL: " . $user->data()->total);
// }

/** 
* COUNT 
**/
// $users = (new User)->find()->count();
// var_dump($users);

/**
* CREATE
**/

// $user = new User();
// $user->first_name = 'Luiz';
// $user->last_name = 'Felipe';
// $user->genre = 'm';
// var_dump($user->save());
// var_dump($user->fail());

/**
* UPDATE
**/

// $user = (new User)->findById(19);
// var_dump($user);
// $user->first_name = 'Diego';
// var_dump($user->save());

/**
* DELETE
**/

// $user = (new User)->findById(20);
// var_dump($user->destroy());
