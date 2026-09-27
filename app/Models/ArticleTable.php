<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTable extends Model
{
    use HasFactory;

    protected $table = 'article_tables';

    protected $fillable = [
        'article_id',
        'sort_order',
        'title',
        'n_rows',
        'n_cols',
        'page_start',
        'page_end',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function cells()
    {
        return $this->hasMany(ArticleTableCell::class, 'table_id');
    }
}
