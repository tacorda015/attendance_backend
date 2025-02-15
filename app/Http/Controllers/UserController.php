<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function GetUser()
    {
        $user_list = User::get();

        return response()->json($user_list);
    }

    public function test()
    {
        return 'Test';   
    }
}
