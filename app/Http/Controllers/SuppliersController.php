<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{
    public function index()
    {
        $data = DB::table('suppliers')->get(['id as value', 'name as text']);
        return response()->json($data);
    }
}
