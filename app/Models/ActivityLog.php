<?php
namespace App\Models;

use App\Models\BaseModel;

class ActivityLog extends BaseModel
{
    public function toSearchableArray()
    {
        return [
            'user_id'       => $this->user_id,
            'module'        => $this->module,
            'event'         => $this->event,
            'description'   => $this->description,
            'status'        => $this->status,
            'old_values'    => $this->old_values,
            'new_values'    => $this->new_values,
            'url'           => $this->url,
            'ip_address'    => $this->ip_address,
            'user_agent'    => $this->user_agent,
        ];
    }

    protected $fillable = [
        'user_id', 'module', 'event', 'description', 'status', 'old_values', 'new_values', 'url', 'ip_address', 'user_agent',
    ];

    protected $table = 'activity_logs';

    protected $casts = [
        'old_values'    => 'array',
        'new_values'    => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
