<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'first_name',
        'last_name',
        'full_name',
        'phone',
        'email',
        'address',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Mutator para generar el nombre completo automáticamente
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $value;
        $this->updateFullName();
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $value;
        $this->updateFullName();
    }

    private function updateFullName()
    {
        if (isset($this->attributes['first_name']) && isset($this->attributes['last_name'])) {
            $this->attributes['full_name'] = $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        }
    }

    // Relación con ventas
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Scope para clientes activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Método para buscar por DNI
    public static function findByDni($dni)
    {
        return static::where('dni', $dni)->first();
    }
}
