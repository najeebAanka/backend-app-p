<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_type',
        'image',
        'target'
    ];

    public function getImageAttribute(){
        return $this->attributes['image'] != "" ? asset('storage/banners/'.$this->attributes['image']) : url('dist/assets/img/empty-auction.jpg');   
    }
}
