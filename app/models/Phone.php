<?php

namespace App\Models;

use Library\Marlon\Modules\Model;

class Phone extends Model
{
    protected $fillables = ["personId", "type", "areaCode", "number", "order", "principal", "status"];
    
    protected $rules = [
        "personId"      => "required|number",
        "type"          => "required|enum:personal,commercial",
        "areaCode"      => "required|max:2",
        "number"        => "required|number|min:8|max:9",
        "status"        => "number",
        "order"         => "number",
        // uniqueValue:the principal address cannot be 1 for other phones for the personId
        "principal"     => "number|uniqueValue:1,personId",
    ];

    protected $table = 'phone';
}