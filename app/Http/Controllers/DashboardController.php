<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Container\Attributes\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
}
