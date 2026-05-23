<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        return Sale::paginate($request->limit ?? 500);
    }
    //
}
