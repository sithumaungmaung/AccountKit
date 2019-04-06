<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id','DESC')->get();

    	return view('users.index',compact('users'));
    }

    public function getAuthUser()
    {
    	

    	return new UserResource(Auth::user());
    }
}
