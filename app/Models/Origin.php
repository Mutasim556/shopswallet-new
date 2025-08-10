<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origin extends Model
{
    use HasFactory;
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function module(){
        return $this->belongsTo(Module::class,'module_id','id');
    }
}
