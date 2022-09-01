<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arr = [
            'id' => (int) $this->id,
            'full_name' => (string) ($this->first_name . " " . $this->last_name),
            'first_name' => (string) $this->first_name,
            'last_name' => (string) $this->last_name,
            'username' => (string) $this->username,
            'photo' => null,
            'email' => (string) $this->email,
            'gender' => $this->gender == User::GENDER_FEMALE ? 'female' : ($this->gender == User::GENDER_MALE ? 'male' : 'none'),
            'created_at' => (string) $this->created_at,
        ];

        // make thumb
        if ($this->photo) {
            $arr['photo'] = $this->photo;
        }

        return $arr;
    }
}
