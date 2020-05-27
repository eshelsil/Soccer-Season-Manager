<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use Exception;

class UsersAPIController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only('index', 'destroy');
        $this->middleware('admin:api')->except('index', 'destroy');
    }


    public function index()
    {
        $res = [];
        $active_user = Auth::user();
        if ($active_user->isAdmin()){
            $users = User::query()->get();
        } else {
            $users = [$active_user];
        }
        
        foreach($users as $user){
            array_push($res, $user->json_export());
        }
        return response()->json($res, 200);
    }


    public function set_admin($id)
    {
        return $this->update_role($id, 'admin');
    }
    

    public function set_regular($id)
    {
        return $this->update_role($id, 'regular');
    }


    private function update_role($id, $role)
    {
        try {
            $user = User::find($id);
            if (is_null($user)){
                $error_msg = 'User id '.$id.' does not exist';
                throw new Exception($error_msg);
            }
            if (Auth::user()->id == $id){
                $error_msg = 'User cannot change his own role';
                throw new Exception($error_msg);
            }
            $user->update(
                ['role' => $role]
            );
            return response()->json($user->json_export(), 200);
        } catch (Exception $e){
            return response($e->getMessage(), 400);
        }
    }


    public function destroy($id)
    {
        try {
            $active_user = Auth::user();
            if (!$active_user->isAdmin() && $active_user->id != $id){
                $error_msg = 'Regular user cannot remove other users';
                throw new Exception($error_msg);
            }
            $user = User::find($id);
            if ($user->isAdmin() && !User::where('id', '!=', $id)->where('role', 'admin')->exists()){
                $error_msg = 'Deleting the only admin user is not allowed';
                throw new Exception($error_msg);
            }
            $user->delete();
            return response()->json([], 200);
        } catch (Exception $e){
            return response($e->getMessage(), 400);
        }
    }

}
