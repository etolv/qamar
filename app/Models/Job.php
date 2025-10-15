<?php

namespace App\Models;

use App\Enums\SectionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\WithFaker;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'section' => SectionEnum::class
    ];

    protected $appends = [
        'section_name'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function getSectionNameAttribute()
    {
        return _t($this->section?->name);
    }
}
