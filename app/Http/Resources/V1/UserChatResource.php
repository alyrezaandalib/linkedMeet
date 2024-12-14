<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->partner->id,
            'name' => $this->partner->name,
            'avatar' => $this->partner->avatar,
            'started_at' => $this->created_at,
            'last_message_at' => $this->updated_at,
        ];
    }
}
