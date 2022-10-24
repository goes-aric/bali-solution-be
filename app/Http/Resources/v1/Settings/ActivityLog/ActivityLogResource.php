<?php
namespace App\Http\Resources\v1\Settings\ActivityLog;

use Carbon\Carbon;
use App\Http\Resources\v1\Pengaturan\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'activity_date' => Carbon::createFromDate($this->created_at)->format('M d, Y'),
            'activity_time' => Carbon::createFromDate($this->created_at)->format('H:i:s'),
            'module'        => $this->module,
            'event'         => $this->event,
            'description'   => $this->description,
            'status'        => $this->status,
            'old_values'    => $this->old_values,
            'new_values'    => $this->new_values,
            'url'           => $this->url,
            'ip_address'    => $this->ip_address,
            'user_agent'    => $this->user_agent,
            'user_id'       => $this->user_id ?? '',
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'user'          => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
