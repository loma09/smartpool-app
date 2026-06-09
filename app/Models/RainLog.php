<?php
namespace App\Models;
<<<<<<< HEAD
=======

>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
use Illuminate\Database\Eloquent\Model;

class RainLog extends Model
{
    protected $fillable = ['device_id', 'rain_value', 'cover_closed', 'notes'];
<<<<<<< HEAD
    protected $casts = ['cover_closed' => 'boolean'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
=======

    protected $casts = ['cover_closed' => 'boolean'];
}
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
