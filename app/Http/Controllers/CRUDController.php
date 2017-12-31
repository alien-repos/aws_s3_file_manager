<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;

class CRUDController extends Controller
{
    public function __construct()
    {
        // $this->input = new ;
    }
    public function create(Request $request)
    {
        $user = new User;

        $user->name = $request->name;

        $user->save();
    }

    public function read(Request $request)
    {
        $user = User::all();

        return view('flight.index', ['flights' => $flights]);
    }

    public function update(Request $request)
    {
        $user = App\User::find(1);

        $user->name = 'New Flight Name';

        $user->save();
    }

    public function delete(Request $request)
    {
        $user = App\User::find(1);

        $user->delete();
    }
}
