<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMyStaffAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = array(
            [
                'name' => 'staff pol',
                'email' => 'staff@polomolok.com',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
            ],
            [
                'name' => 'staff koronadal',
                'email' => 'staff@koronadal.com',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 1
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
