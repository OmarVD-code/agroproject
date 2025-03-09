<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpGetAllCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE get_all_customers(
            IN pPage INT,
            IN pPerPage INT,
            IN pSearch VARCHAR(255),
            IN pOrderBy VARCHAR(255),
            IN pOrderDir VARCHAR(255)
        )
            BEGIN
                DECLARE totalRows INT;
                DECLARE vPage INT;

                DROP TEMPORARY TABLE IF EXISTS tmp_customers;
                CREATE TEMPORARY TABLE IF NOT EXISTS tmp_customers(
                    SELECT 
                        c.id,
                        c.name,
                        c.dni,
                        c.phone,
                        c.address_id,
                        ca.name address,
                        c.created_at
                    FROM customers c
                    LEFT JOIN customer_addresses ca on ca.id = c.address_id
                    WHERE (pSearch IS NULL OR c.name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', pSearch, '%'))
                );

                SELECT COUNT(*) FROM tmp_customers INTO totalRows;
                SET vPage = pPerPage * (pPage - 1);

                SET @query = CONCAT(\"SELECT \", totalRows, \" as totalRows, tc.* FROM tmp_customers tc ORDER BY \", pOrderBy, \" \", pOrderDir, \" LIMIT \", pPerPage, \" OFFSET \", vPage);

                PREPARE stmt FROM @query;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_customers");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_customers");
    }
}
