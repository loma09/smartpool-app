<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChlorineLog extends Model
{
    protected $fillable = [
        'device_id', 'turbidity_value', 'turbidity_status',
        'chlorine_added', 'chlorine_amount_ml', 'notes',
    ];
    protected $casts = ['chlorine_added' => 'boolean'];
}
