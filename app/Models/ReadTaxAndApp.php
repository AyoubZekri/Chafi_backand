<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadTaxAndApp extends Model
{
    use HasFactory;

    protected $fillable = ['tax_and_app_id','id_read','user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxAndApp()
    {
        return $this->belongsTo(TaxAndApp::class);
    }
}
