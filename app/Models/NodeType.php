<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NodeType extends Model
{
    use HasFactory;

    protected $table = 'node_types';
    public $timestamps = false;

    protected $fillable = [
        'name_ar',
        'name_fr',
        'level_rank',
    ];
}
