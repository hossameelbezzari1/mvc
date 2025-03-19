<?php

namespace App\Controllers;

use function view;

use Symfony\Component\HttpFoundation\Request;
use App\Models\Visitor;
use App\Models\Form;

class AdminController
{
    public function login(Request $request)
    {
        return view('admin/login', [
            'title' => 'Admin Login',
            'layout' => 'master'
        ]);
    }

    public function dashboard(Request $request)
    {
        $visitors = Visitor::all();
        return view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'layout' => 'master',
            'visitors' => $visitors
        ]);
    }

    public function visitorDetails(Request $request, $id)
    {
        $visitor = Visitor::find($id);
        $forms = Form::findByVisitorId($id);

        return view('admin/visitor_details', [
            'title' => 'Visitor Details',
            'layout' => 'master',
            'visitor' => $visitor,
            'forms' => $forms
        ]);
    }
}
