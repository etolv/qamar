<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Brand extends Model implements TranslatableContract, HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use Translatable;
    use InteractsWithMedia;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function getTranslatedFields()
    {
        $self = new static;
        return $self->translatedAttributes;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
