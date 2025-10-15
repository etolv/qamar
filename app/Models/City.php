<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model implements TranslatableContract
{
    use HasFactory, Translatable, SoftDeletes;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public static function getTranslatedFields()
    {
        $self = new static;
        return $self->translatedAttributes;
    }
}
