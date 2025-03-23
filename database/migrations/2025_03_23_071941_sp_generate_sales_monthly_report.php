<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpGenerateSalesMonthlyReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "CREATE PROCEDURE generate_sales_monthly_report(
            IN pYear INT,
            IN pMonth INT
        )
            BEGIN
                DECLARE from_date DATE;
                DECLARE to_date DATE;

                SET @from_date = STR_TO_DATE(CONCAT(pYear, '-', LPAD(pMonth, 2, '0'), '-01'), '%Y-%m-%d');
                SET @to_date = DATE_SUB(DATE_ADD(@from_date, INTERVAL 1 MONTH), INTERVAL 1 SECOND);

                SELECT p.name, sd.quantity
                FROM sales_details sd
                JOIN sales s ON s.id = sd.sale_id
                JOIN products p ON p.id = sd.product_id
                WHERE s.date BETWEEN @from_date AND @to_date
                AND s.status NOT IN (3);
            END
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS generate_sales_monthly_report");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS generate_sales_monthly_report");
    }
}
