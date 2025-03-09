<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;

        $parameters = [
            $page,
            $perPage,
            $request->search,
            $request->orderBy,
            $request->orderDir,
        ];

        $statement = "CALL get_all_customers(?, ?, ?, ?, ?)";
        $data = DB::select($statement, $parameters);

        if (count($data)) {
            $totalCount = $data[0]->totalRows;
        } else {
            $totalCount = 0;
        }

        $results = collect($data);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($results, $totalCount, $perPage, $page);
        return $paginator;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('customers')->insert(
                [
                    'name' => $request->name,
                    // 'phone' => $request->phone,
                    // 'address_id' => $request->address_id,
                    // 'dni' => $request->dni,
                    'created_at' => now(),
                ]
            );
            DB::commit();
            return response()->json(['message' => 'Cliente creado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al crear cliente', 'error' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('customers')->where('id', $request->id)->update(
                [
                    'name' => $request->name,
                    // 'phone' => $request->phone,
                    // 'address_id' => $request->address_id,
                    // 'dni' => $request->dni,
                    'updated_at' => now(),
                ]
            );
            DB::commit();
            return response()->json(['message' => 'El cliente ha sido actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al actualizar cliente', 'error' => $th->getMessage()], 500);
        }
    }

    public function getCustomers()
    {
        return DB::table('customers')->get();
    }
}
