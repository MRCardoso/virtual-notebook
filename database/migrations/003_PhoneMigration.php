<?php

namespace Database\Migrations;

use Library\Marlon\DB\Migration;

class PhoneMigration extends Migration
{
    public function up()
    {
        $this->create('phone', function($table){
            $table->increment('id');
            $table->integer('personId')->notNull();
            $table->enum('type', ['personal', 'commercial'])->notNull();
            $table->string('areaCode', 2)->notNull();
            $table->string('number', 9)->notNull();
            $table->integer('order');
            $table->integer('principal');
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('personId', 'id', 'person', 'cascade');
        });
    }

    public function down()
    {
        $this->drop('phone');
    }
}