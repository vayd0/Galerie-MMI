<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PossedeTag extends Model
{
    protected $table = 'possede_tag';
    public $timestamps = false;
    protected $fillable = ['photo_id', 'tag_id'];
}
