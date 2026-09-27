<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTableCell extends Model
{
    use HasFactory;

    protected $table = 'article_table_cells';

    protected $fillable = [
        'table_id',
        'row_idx',
        'col_idx',
        'row_span',
        'col_span',
        'is_header',
        'text',
        'value_num',
        'updated_by',
    ];

    public function table()
    {
        return $this->belongsTo(ArticleTable::class, 'table_id');
    }
}
