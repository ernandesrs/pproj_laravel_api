<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeUpdateFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MeController extends Controller
{
    /**
     * @return UserResource
     */
    public function index()
    {
        return new UserResource(auth()->user());
    }

    /**
     * @param MeUpdateFormRequest $request
     * @return UserResource
     */
    public function update(MeUpdateFormRequest $request)
    {
        /** @var User $me */
        $me = auth()->user();

        $validated = $request->validated();

        $me->first_name = $validated["first_name"];
        $me->last_name = $validated["last_name"];
        $me->username = $validated["username"];
        $me->gender = $validated["gender"];

        // photo
        if ($photo = $validated["photo"] ?? null) {
            if ($me->photo) {
                Storage::delete("public/{$me->photo}");
            }

            $me->photo = $photo->store("avatars", "public");
        }

        // password
        if ($password = $validated["password"] ?? null)
            $me->password = Hash::make($password);

        $me->save();

        return new UserResource($me);
    }
}
