<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;

        $parameters = [
            $page,
            $perPage,
            $request->search,
            $request->status,
            $request->year,
            $request->month,
            $request->orderBy,
            $request->orderDir,
        ];

        $statement = "CALL get_all_sales(?, ?, ?, ?, ?, ?, ?, ?)";
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
            $saleId = DB::table('sales')->insertGetId(
                [
                    'customer_id' => $request->client_id,
                    'address_id' => $request->address_id,
                    'code' => $request->code,
                    'date' => $request->date,
                    'created_at' => now(),
                ]
            );

            foreach ($request->products as $product) {
                DB::table('sales_details')->insert(
                    [
                        'sale_id' => $saleId,
                        'product_id' => $product['id'],
                        'unit_price' => $product['unit_price'],
                        'quantity' => $product['quantity'],
                        'discount' => 0,
                        'created_at' => now(),
                    ]
                );
            }
            DB::commit();
            return response()->json(['message' => 'La venta ha sido registrada correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al crear venta', 'error' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        Log::info($request->all());
        try {
            DB::beginTransaction();
            DB::table('sales')
                ->where('id', $request->sale_id)
                ->update([
                    'customer_id' => $request->client_id,
                    'address_id' => $request->address_id,
                    'code' => $request->code,
                    'date' => $request->date,
                    'updated_at' => now()
                ]);

            foreach ($request->products as $product) {
                if ($product['deleted']) {
                    DB::table('sales_details')
                        ->where('id', $product['sale_detail_id'])
                        ->delete();
                    continue;
                }

                if ($product['modified']) {
                    DB::table('sales_details')
                        ->where('id', $product['sale_detail_id'])
                        ->update(
                            [
                                'unit_price' => $product['unit_price'],
                                'quantity' => $product['quantity'],
                                'updated_at' => now(),
                            ]
                        );
                }
            }
            DB::commit();
            return response()->json(['message' => 'La venta ha sido actualizada correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al actualizar venta', 'error' => $th->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('sales')->where('id', $request->id)->update(['status' => $request->new_status]);
            DB::commit();
            return response()->json(['message' => 'El estado de la venta ha sido actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(['message' => 'Error al actualizar venta', 'error' => $th->getMessage()], 500);
        }
    }

    public function details(Request $request)
    {
        $statement = "CALL sp_get_sale_detail(?)";
        $data = DB::select($statement, [$request->sale_id]);
        return response()->json($data);
    }
}
