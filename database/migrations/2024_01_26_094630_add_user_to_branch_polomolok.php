<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserToBranchPolomolok extends Migration
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
                'name' => 'babynalenedelacruz',
                'email' => 'babynalenedelacruz@gmail.com',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
            ],
            [
                'name' => 'bajunaidali6',
                'email' => 'bajunaidali6@gmail.com',
                'password' => bcrypt('mypassword123*'),
                'foto' => '/img/user.jpg',
                'level' => 2,
                'branch_id' => 2
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
