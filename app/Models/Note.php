<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';
    public $timestamps = false; // Note: migration has created_at but no updated_at, let's just let Eloquent handle created_at if we want, or disable it

    protected $fillable = [
        'article_id',
        'node_id',
        'note_type',
        'text',
        'sort_order',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id');
    }
}
