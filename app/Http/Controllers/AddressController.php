<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        return DB::table('customer_addresses')->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $lastInsertedId = DB::table('customer_addresses')->insertGetId([
                'name' => $request->name,
                'created_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'Dirección creada correctamente', 'id' => $lastInsertedId], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al crear dirección', 'error' => $th->getMessage()], 500);
        }
    }
}
