<?php

namespace Database\Migrations;

use Library\Marlon\DB\Migration;

class PersonMigration extends Migration
{
    public function up()
    {
        $this->create('person', function($table){
            $table->increment('id');
            $table->integer('userId')->notNull();
            $table->string('name', 80)->notNull();
            $table->string('lastName', 80)->notNull();
            $table->string('nickname', 80)->notNull();
            $table->integer('status')->default(1);
            $table->enum('sex', ['M', 'F']);
            $table->timestamps();

            $table->foreign('userId', 'id', 'user', 'cascade');
        });
    }

    public function down()
    {
        $this->drop('person', 'CASCADE');
    }
}