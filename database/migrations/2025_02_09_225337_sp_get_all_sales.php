<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpGetAllSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE get_all_sales(
            IN pPage INT,
            IN pPerPage INT,
            IN pSearch VARCHAR(255),
            IN pStatus INT,
            IN pYear INT,
            IN pMonth INT,
            IN pOrderBy VARCHAR(255),
            IN pOrderDir VARCHAR(255)
        )
            BEGIN
                DECLARE totalRows INT;
                DECLARE totalAmount DECIMAL(10,2);

                DECLARE vPage INT;
                DECLARE vStartDate DATE;
                DECLARE vEndDate DATE;

                SET vStartDate = STR_TO_DATE(CONCAT(pYear, '-', LPAD(pMonth, 2, '0'), '-01'), '%Y-%m-%d');
                SET vEndDate = DATE_SUB(DATE_ADD(vStartDate, INTERVAL 1 MONTH), INTERVAL 1 SECOND);

                DROP TEMPORARY TABLE IF EXISTS tmp_sales;
                CREATE TEMPORARY TABLE IF NOT EXISTS tmp_sales (
                    SELECT
                        s.id,
                        s.code,
                        c.id client_id,
                        c.name client,
                        ca.id address_id,
                        ca.name address,
                        s.`date`,
                        s.status,
                        s.status+0 status_int,
                        sum(sd.unit_price*sd.quantity-sd.discount) amount,
                        false is_edit
                    from sales s
                    join customers c on c.id = s.customer_id
                    join customer_addresses ca ON ca.id = s.address_id
                    join sales_details sd on sd.sale_id = s.id
                    where s.date BETWEEN vStartDate AND vEndDate
                    and (pStatus is null or s.status = pStatus)
                    and (pSearch IS NULL OR c.name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', pSearch, '%'))
                    group by 1,2,3,4,5,6
                );

                SELECT COUNT(*) FROM tmp_sales INTO totalRows;
                SELECT SUM(ts.amount) INTO totalAmount FROM tmp_sales as ts;
                SET vPage = pPerPage * (pPage - 1);
                
                SET @query = CONCAT(\"SELECT \", totalRows, \" as totalRows, \", totalAmount ,\" as totalAmount, ts.* FROM tmp_sales ts ORDER BY \", pOrderBy, \" \", pOrderDir, \" LIMIT \", pPerPage, \" OFFSET \", vPage);
                
                PREPARE stmt FROM @query;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            END
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_sales");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS get_all_sales");
    }
}
