<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'document_id',
        'node_id',
        'part',
        'label',
        'number',
        'number_int',
        'sort_order',
        'text',
        'is_repealed',
        'page_start',
        'page_end',
        'version',
        'updated_by',
        'change_reason',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'article_id');
    }

    public function tables()
    {
        return $this->hasMany(ArticleTable::class, 'article_id');
    }
}
