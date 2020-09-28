<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;
use App\Models\Language;

class Movie extends Model
{
    use HasFactory;

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function scopeWhereLike($query, $column, $value)
    {
        return $query->where($column, 'like', '%' . $value . '%');
    }
}
