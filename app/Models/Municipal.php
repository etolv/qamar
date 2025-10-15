<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipal extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    use SoftDeletes;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public static function getTranslatedFields()
    {
        $self = new static;
        return $self->translatedAttributes;
    }
}
