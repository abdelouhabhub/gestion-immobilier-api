<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'rooms',
        'surface',
        'price',
        'city',
        'neighborhood',
        'description',
        'status',
        'published'
    ];

    protected $casts = [
        'published' => 'boolean',
        'price' => 'decimal:2',
        'surface' => 'decimal:2'
    ];

    // Génération automatique du titre
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            $property->title = $property->generateTitle();
        });

        static::updating(function ($property) {
            $property->title = $property->generateTitle();
        });
    }

    public function generateTitle(): string
    {
        $title = $this->type;

        if ($this->rooms) {
            $title .= " {$this->rooms} pièces";
        }

        $title .= " à {$this->city}";

        if ($this->neighborhood) {
            $title .= " - {$this->neighborhood}";
        }

        return $title;
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
