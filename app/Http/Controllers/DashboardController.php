<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use App\Tracker;
use App\Stream;
use App\User;

class DashboardController extends BaseController
{
    public function __construct()
    {
        if(User::count() > 0) {
            $this->middleware('auth');
        }
    }
    public function index()
    {
        if(User::count() == 0)
        {
            return view('user.create')->with('title', 'Create Initial User to Secure Dashboard');
        }
        $streams = Stream::all();
        return view('dashboard.index')
        ->with('title', 'Stream Dashboard')
        ->with('streams', $streams);
    }
}
