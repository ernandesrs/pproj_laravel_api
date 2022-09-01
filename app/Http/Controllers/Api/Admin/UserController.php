<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreAndUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
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

        $resources = $users->map(function ($user) {
            return new UserResource($user);
        });

        return response()->json([
            'users' => $resources,
            "pagination" => $users->links()->render()
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
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'confirmation_token' => Str::random(25),
            'gender' => $validated['gender'] ?? User::GENDER_NONE,
            'password' => Hash::make($validated['password']),
        ]);

        event(new UserRegistered($user));

        return response()->json([
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        //
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

        $users = User::whereNotNull("id")->orderBy("created_at", $validated["order"]);

        return $users->paginate($validated["limit"]);
    }
}