<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ProductCategory extends Model
{
    use SoftDeletes,  Sluggable,SoftDeletes;

    protected $appends = [
        'photo',
    ];

    public $table = 'product_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'slug',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');

    }

    public function parentCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');

    }

    public function childCategories()
    {
        return $this->hasMany(ProductCategory::class, 'category_id');

    }

    public function products()
    {
        return $this->belongsToMany(Product::class);

    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
