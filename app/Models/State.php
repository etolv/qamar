<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model implements TranslatableContract
{
    use HasFactory, Translatable;
    use SoftDeletes;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function stateTranslations()
    {
        return $this->hasMany(StateTranslation::class);
    }

    public static function getTranslatedFields()
    {
        $self = new static;
        return $self->translatedAttributes;
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
