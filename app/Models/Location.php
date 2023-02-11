<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $address
 * @property ?float $latitude
 * @property ?float $longitude
 * @property ?Carbon $synced_at
 * @property ?Carbon $scheduled_at
 * @property ?Status $status
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = ['address'];

    protected $casts = [
        'synced_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'status' => Status::class,
    ];
}
