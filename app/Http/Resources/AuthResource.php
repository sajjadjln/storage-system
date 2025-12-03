<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this['user']),
            'token' => $this['accessToken'],
            'token_type' => 'Bearer',
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'message' => 'login successful',
        ];
    }
}
