<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'opening_cash',
        'closing_cash',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_time'   => 'datetime',
            'end_time'     => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
        ];
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper cek shift masih aktif
    public function isActive(): bool
    {
        return is_null($this->end_time);
    }

    // Hitung durasi shift
    public function duration(): string
    {
        if (!$this->end_time) return 'Sedang berjalan';
        $diff = $this->start_time->diff($this->end_time);
        return $diff->h . ' jam ' . $diff->i . ' menit';
    }
}