<?php

namespace App\Controllers;

use App\Models\Users;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function view;

class HomeController
{
    public function index(Request $request)
    {
        $users = Users::all();
        // dd($users);
        return view('pages/home', ['title' => 'Home Page', 'layout' => 'master']);
    }
}
