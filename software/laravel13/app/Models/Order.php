<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'address', 'serial_no', 'dress_no',
        'reference_name', 'reference_phone', 'booking_date',
        'delivery_date', 'status'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'delivery_date' => 'date',
    ];
}