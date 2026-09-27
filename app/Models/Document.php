<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'code',
        'title_ar',
        'title_fr',
        'year',
        'source_file',
        'file',
        'pages',
        'legal_basis',
        'preamble',
        'is_active',
    ];
}
