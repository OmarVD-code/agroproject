<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpGetAllProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE get_all_products(
            IN pPage INT,
            IN pPerPage INT,
            IN pSearch VARCHAR(255),
            IN pOrderBy VARCHAR(255),
            IN pOrderDir VARCHAR(255)
        )
            BEGIN
                DECLARE totalRows INT;
                DECLARE vPage INT;

                DROP TEMPORARY TABLE IF EXISTS tmp_products;
                CREATE TEMPORARY TABLE IF NOT EXISTS tmp_products (
                    SELECT 
                        p.id,
                        p.name name,
                        p.unit_price,
                        p.stock,
                        p.created_at,
                        s.id supplier_id,
                        s.name supplier_name
                    FROM products p
                    JOIN suppliers s on s.id = p.supplier_id
                    WHERE (pSearch IS NULL OR p.name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', pSearch, '%'))
                );

                SELECT COUNT(*) FROM tmp_products INTO totalRows;
                SET vPage = pPerPage * (pPage - 1);

                SET @query = CONCAT(\"SELECT \", totalRows, \" as totalRows, tp.* FROM tmp_products tp ORDER BY \", pOrderBy, \" \", pOrderDir, \" LIMIT \", pPerPage, \" OFFSET \", vPage);
                
                PREPARE stmt FROM @query;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_products");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_products");
    }
}
