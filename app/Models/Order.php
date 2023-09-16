<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'payment_id',
        'uuid',
        'products',
        'address',
        'delivery_fee',
        'amount',
        'shipped_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivery_fee' => 'double',
        'amount' => 'double',
        'shipped_at' => 'datetime',
        'products' => 'array',
        'address' => 'array',
    ];

    /**
     * A order belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : object
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A order belongs to a order_status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order_status() : object
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * A order belongs to a payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment() : object | null
    {
        return $this->belongsTo(Payment::class);
    }
}
