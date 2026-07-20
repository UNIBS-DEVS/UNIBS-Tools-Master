<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Timesheet;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        //  dd("test");

    //      dd([
    //     'default_connection' => DB::getDefaultConnection(),
    //     'database' => DB::connection()->getDatabaseName(),
    // ]);
        return view('dashboard.index');
    }
}
