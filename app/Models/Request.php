<?php

namespace App\Models;

use App\Events\RequestUpdatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'status',
        'user_id',
        'message',
        'comment',
        'admin_id',
    ];

    protected $dispatchesEvents = [
        'updated' => RequestUpdatedEvent::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeFilterByDate($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $startDateTime = $startDate . " 00:00:00";
            $endDateTime = $endDate . " 23:59:59";
            return $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
        }

        return $query;
    }
}
