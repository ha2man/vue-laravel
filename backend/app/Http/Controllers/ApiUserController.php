<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Kyc;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Carbon\Carbon;

class ApiUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::select(['users.*', 'kycs.status', 'kycs.id AS kid'])
            ->leftJoin('kycs', 'users.id', '=', 'kycs.user_id');
        if ($request->status) {
            $users->where('kycs.status', $request->status);
        }
        if ($request->filters) {
            $filter = json_decode($request->filters[0]);
            $users->where(function($query) use ($filter) {
                $query->where('users.first_name', 'like', '%' . $filter->value . '%');
                $query->orWhere('users.last_name', 'like', '%' . $filter->value . '%');
                $query->orWhere('users.uid', 'like', '%' . $filter->value . '%');
                $query->orWhere('users.email', 'like', '%' . $filter->value . '%');
            });
        }
        if ($request->sorters) {
            $sorter = json_decode($request->sorters[0]);
            $users->orderBy('users.'.$sorter->field, $sorter->dir);
        }
        $users->with(['roles']);
        return response()->json($users->paginate($request->size));
    }

    public function search($uid) {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = [];
        if ($uid) {
            $users = User::select(['id', 'uid', 'first_name', 'last_name'])
                ->where('uid', 'like', '%'.$uid.'%')->get();
        }

        return response()->json([
            'success' => true,
            'users' => new UserResource($users)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->all();
        $data['password'] = str_random(10);
        $user = User::create($data);
        Kyc::create([
            'user_id' => $user->id
        ]);

        // generate unique id for search
        $user->uid = $user->first_name . sprintf('%05d', $user->id);
        $user->save();

        // assign user role
        $user->assignRole($request->roles);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->all());

        // assign user role
        $user->syncRoles($request->roles);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function verify(User $user) {
        abort_if(Gate::denies('user_verify'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->kyc_verified_at = $user->kyc_verified_at ? null : Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

        return \response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Kyc::where('user_id', $user->id)->delete();
        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
