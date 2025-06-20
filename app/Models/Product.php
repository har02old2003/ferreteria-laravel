<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'brand_id',
        'supplier_id',
        'sku',
        'price',
        'stock',
        'minimum_stock',
        'image',
        'is_active',
        'has_expiration',
        'expiration_date',
    ];



    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'is_active' => 'boolean',
        'has_expiration' => 'boolean',
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['formatted_code', 'expiration_status', 'stock_status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getFormattedCodeAttribute()
    {
        return $this->sku;
    }

    public function getExpirationStatusAttribute()
    {
        if (!$this->has_expiration || !$this->expiration_date) {
            return null;
        }

        $today = Carbon::today();
        $expirationDate = Carbon::parse($this->expiration_date);
        $monthsUntilExpiration = $today->diffInMonths($expirationDate, false);

        if ($monthsUntilExpiration < 0) {
            return 'expired'; // Producto caducado
        } elseif ($monthsUntilExpiration <= 3) {
            return 'near_expiration'; // Próximo a caducar (3 meses o menos)
        } else {
            return 'valid'; // Vigente
        }
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= $this->minimum_stock) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function setExpirationDateAttribute($value)
    {
        if ($this->has_expiration && $value === null) {
            // Si tiene caducidad y no se proporciona fecha, establecer 18 meses desde hoy
            $this->attributes['expiration_date'] = Carbon::today()->addMonths(18)->format('Y-m-d');
        } else {
            $this->attributes['expiration_date'] = $value;
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpiringSoon($query, $months = 3)
    {
        return $query->where('has_expiration', true)
                    ->whereNotNull('expiration_date')
                    ->whereDate('expiration_date', '>', Carbon::now())
                    ->whereDate('expiration_date', '<=', Carbon::now()->addMonths($months));
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= minimum_stock');
    }
}
