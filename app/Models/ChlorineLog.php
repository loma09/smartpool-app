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
<<<<<<< HEAD

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
=======
}
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
