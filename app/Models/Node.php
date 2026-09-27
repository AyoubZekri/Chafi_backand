<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $table = 'nodes';

    protected $fillable = [
        'document_id',
        'parent_id',
        'node_type_id',
        'part',
        'label',
        'title',
        'sort_order',
        'page',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function nodeType()
    {
        return $this->belongsTo(NodeType::class, 'node_type_id');
    }

    public function parent()
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Node::class, 'parent_id');
    }
}
