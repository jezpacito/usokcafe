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
                'password' => bcrypt('password'),
                'foto' => '/img/user.jpg',
                'level' => 1
            ],
            [
                'name' => 'Den Bautista',
                'email' => 'den.bautista@mail.com',
                'password' => bcrypt('usok.denbautista'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
            ],
            [
                'name' => 'John Doe',
                'email' => 'staff@mail.com',
                'password' => bcrypt('password'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
            ]
        );

        array_map(function (array $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }, $users);
    }
}
