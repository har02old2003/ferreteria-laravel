<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'customer_name',
        'code',
        'total',
        'status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            // Generar código único para la venta
            $lastSale = static::withTrashed()->latest('id')->first();
            $nextId = $lastSale ? $lastSale->id + 1 : 1;
            $sale->code = 'V' . str_pad($nextId, 8, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_product')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
} 