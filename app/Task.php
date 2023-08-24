<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'id','title', 'description', 'due_date', 'status','user_id'
    ];

    protected $hidden = [
        
    ];

}