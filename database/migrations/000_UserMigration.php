<?php

namespace Database\Migrations;

use Library\Marlon\DB\Migration;

class UserMigration extends Migration
{
    public function up()
    {
        $this->create('user', function($table){
            $table->increment('id');
            $table->string('name', 80)->notNull();
            $table->string('email', 80)->notNull();
            $table->string('username', 80)->notNull();
            $table->string('password', 255)->notNull();
            $table->string('resetToken', 255);
            $table->string('authToken', 255);
            $table->datetime('resetExpires');
            $table->datetime('passwordChange');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->drop('user', 'CASCADE');
    }
}