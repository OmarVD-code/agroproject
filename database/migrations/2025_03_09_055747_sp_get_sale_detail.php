<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpGetSaleDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE sp_get_sale_detail(
            IN pSaleId INT
        )
            BEGIN
                select
                -- sd.id sale_detail_id,
                p.id,
                p.name,
                sd.quantity,
                sd.unit_price,
                (sd.quantity*sd.unit_price) amount
            from sales_details sd
            join products p on p.id = sd.product_id
            where sd.sale_id = pSaleId;
            END
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_sale_detail");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
