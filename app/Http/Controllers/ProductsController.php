<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
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

        $statement = "CALL get_all_products(?, ?, ?, ?, ?)";
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

    public function getProducts()
    {
        $data = DB::table('products')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('products')->insert(
                [
                    'name' => $request->name,
                    'category_id' => $request->category,
                    'unit_price' => $request->unit_price,
                    'stock' => $request->stock,
                    'supplier_id' => $request->supplier_id,
                    'created_at' => now(),
                ]
            );
            DB::commit();
            return response()->json(['message' => 'Producto creado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al crear producto', 'error' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('products')->where('id', $request->id)->update(
                [
                    'name' => $request->name,
                    'unit_price' => $request->unit_price,
                    'stock' => $request->stock,
                    'supplier_id' => $request->supplier_id,
                    'updated_at' => now(),
                ]
            );
            DB::commit();
            return response()->json(['message' => 'El producto ha sido actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al actualizar producto', 'error' => $th->getMessage()], 500);
        }
    }
}
