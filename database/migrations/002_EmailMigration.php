<?php

namespace Database\Migrations;

use Library\Marlon\DB\Migration;

class EmailMigration extends Migration
{
    public function up()
    {
        $this->create('email', function($table){
            $table->increment('id');
            $table->integer('personId')->notNull();
            $table->string('email', 120)->notNull();
            $table->enum('type', ['personal', 'commercial'])->notNull();
            $table->integer('order');
            $table->integer('principal')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('personId', 'id', 'person', 'cascade');
        });
    }

    public function down()
    {
        $this->drop('email');
    }
}