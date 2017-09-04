<?php

namespace App\Models;

use Library\Marlon\Modules\Model;

class Email extends Model
{
    protected $fillables = ["personId", "email", "type", "order", "principal", "status"];

    protected $rules = [
        "personId"      => "required|number",
        "email"         => "required|email|min:3|max:115",
        "type"          => "required|enum:personal,commercial",
        "status"        => "required|number",
        "order"         => "number",
        "principal"     => "number|uniqueValue:1,personId",
    ];

    protected $table = 'email';
}