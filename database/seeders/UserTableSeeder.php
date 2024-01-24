<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         Branch::insert([
            [
                'name' => 'Koronadal',
                'address' => 'Koronadal'
            ],
            [
                'name' => 'Polomolok',
                'address' => 'Polomolok'
            ],
        ]);

        $users = array(
            [
                'name' => 'Administrator',
                'email' => 'admin@mail.com',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 1
            ],
            [
                'name' => 'John Doe',
                'email' => 'staff@mail.com',
                'password' => bcrypt('password'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
            ],
            [
                'name' => 'Admin Kingbo',
                'email' => 'aldrico@usokcafe.store',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 1
            ],
        );

        array_map(function (array $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }, $users);
    }
}
