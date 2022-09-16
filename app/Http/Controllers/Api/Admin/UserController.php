<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\UserRegistered;
use App\Exceptions\Admin\NotHavePermission;
use App\Helpers\Thumb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserPhotoStoreRequest;
use App\Http\Requests\Admin\UserStoreAndUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * @var string
     */
    private $photoDir = "avatars";

    /**
     * @var integer
     */
    private $maxLimit = 32;

    /**
     * @var integer
     */
    private $limit = 12;

    /**
     * @var array
     */
    private $order = ['asc', 'desc'];

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $users = $this->filter($request);

        $userResources = $users->map(function ($user) {
            return new UserResource($user);
        });

        $pagination = $users->toArray();
        unset($pagination["data"], $pagination["links"]);

        return response()->json([
            "users" => $userResources,
            "pagination" => $pagination
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserStoreAndUpdateRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreAndUpdateRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            "first_name" => $validated["first_name"],
            "last_name" => $validated["last_name"],
            "username" => $validated["username"],
            "email" => $validated["email"],
            "confirmation_token" => Str::random(25),
            "gender" => $validated["gender"] ?? User::GENDER_NONE,
            "password" => Hash::make($validated["password"]),
        ]);

        event(new UserRegistered($user));

        return response()->json([
            "user" => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return response()->json([
            "user" => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserStoreAndUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserStoreAndUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->first_name = $validated["first_name"];
        $user->last_name = $validated["last_name"];
        $user->username = $validated["username"];
        $user->gender = $validated["gender"] ?? $user->gender;

        // photo
        if ($photo = $validated["photo"] ?? null) {
            if ($user->photo) {
                Thumb::clear($user->photo);
                Storage::delete("public/{$user->photo}");
            }

            $user->photo = $photo->store($this->photoDir, "public");
        }

        if ($password = $validated["password"] ?? null)
            $user->password = Hash::make($password);

        $user->save();

        return response()->json([
            "user" => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws NotHavePermission
     */
    public function destroy(User $user)
    {
        $logged = auth()->user();

        if ($logged->id == $user->id || $logged->level <= $user->level) {
            throw new NotHavePermission();
        }

        $user->delete();

        return response()->json([]);
    }

    /**
     * Store the uploaded user photo
     *
     * @param UserPhotoStoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function photoStore(UserPhotoStoreRequest $request, User $user)
    {
        if ($user->photo)
            $this->photoDelete($user);

        $user->photo = $request->file("photo")->store($this->photoDir, "public");
        $user->save();

        return response()->json([
            "user" => (new UserResource($user)),
            "photo_url" => Storage::url($user->photo)
        ]);
    }

    /**
     * Delete the uploaded user photo
     *
     * @param User $user
     * @return JsonResponse
     */
    public function photoDelete(User $user)
    {
        if ($user->photo) {
            Thumb::clear($user->photo);
            Storage::disk("public")->delete("{$user->photo}");
            $user->photo = null;
            $user->save();
        }

        return response()->json([
            "user" => (new UserResource($user)),
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function promote(User $user)
    {
        $logged = auth()->user();
        if ($logged->id == $user->id || $logged->level != User::LEVEL_MASTER || $user->level == User::LEVEL_8)
            throw new NotHavePermission();

        if ($user->level == User::LEVEL_1)
            $user->level = User::LEVEL_2;
        else if ($user->level == User::LEVEL_2)
            $user->level = User::LEVEL_8;

        $user->save();

        return response()->json([
            "user" => new UserResource($user)
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function demote(User $user)
    {
        $logged = auth()->user();
        if ($logged->id == $user->id || $logged->level != User::LEVEL_MASTER || $user->level == User::LEVEL_1)
            throw new NotHavePermission();

        if ($user->level == User::LEVEL_8)
            $user->level = User::LEVEL_2;
        else if ($user->level == User::LEVEL_2)
            $user->level = User::LEVEL_1;

        $user->save();

        return response()->json([
            "user" => new UserResource($user)
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function filter(Request $request)
    {
        $validated = [
            'limit' => filter_var($request->get("limit"), FILTER_VALIDATE_INT),
            'order' => filter_var($request->get("order"))
        ];

        if (!$validated["limit"] || $validated["limit"] > $this->maxLimit)
            $validated["limit"] = $this->limit;

        if (!$validated["order"] || !in_array($validated["order"], $this->order))
            $validated["order"] = $this->order[1];

        $users = User::whereNotNull("id")->orderBy("level", $this->order[1])->orderBy("created_at", $validated["order"]);

        return $users->paginate($validated["limit"]);
    }
}
