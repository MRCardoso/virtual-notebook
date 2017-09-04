<?php

namespace App\Models;

use Library\Marlon\Modules\Model;

class Person extends Model
{
    protected $fillables = ["userId", "name", "lastName", "nickname", "status","sex"];
    
    protected $rules = [
        "userId"    => "required",
        "name"      => "required|min:3|max:75|unique:person",
        "lastName"  => "required|max:75",
        "sex"       => "required",
        "nickname"  => "required|max:75",
        "status"    => "number",
        "sex"       => "enum:F,M"
    ];

    protected $table = "person";

    protected function relations()
    {
        return [
            "emails" => [Email::class, "personId", "id"],
            "phones" => [Phone::class, "personId", "id"]
        ];
    }
}