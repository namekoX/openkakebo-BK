<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder {
    use \Illuminate\Foundation\Testing\WithFaker;
    public function run() {
     $users = factory(App\User::class, 5)->create();
    }
   }