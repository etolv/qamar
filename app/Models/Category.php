<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Category extends Model implements TranslatableContract, HasMedia
{
    use Translatable;
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    public $translatedAttributes = ['name'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // public function translations()
    // {
    //     return $this->hasMany(CategoryTranslation::class, 'category_id');
    // }


    public static function getTranslatedFields()
    {
        $self = new static;
        return $self->translatedAttributes;
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'category_id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'category_id')->with('categories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
