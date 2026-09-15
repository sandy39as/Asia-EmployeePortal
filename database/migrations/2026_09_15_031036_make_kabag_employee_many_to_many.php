<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kabag_employee')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. TAMBAH INDEX BIASA UNTUK FOREIGN KEY employee_id
        |--------------------------------------------------------------------------
        |
        | Unique lama employee_id saat ini sedang dipakai untuk FK.
        | Jadi kita buat index biasa terlebih dahulu agar FK tetap punya index.
        |
        */

        $indexes = collect(
            DB::select('SHOW INDEX FROM `kabag_employee`')
        )->groupBy('Key_name');

        $hasNormalEmployeeIndex =
            $indexes->contains(
                function ($rows) {
                    $columns =
                        $rows
                            ->sortBy('Seq_in_index')
                            ->pluck('Column_name')
                            ->values()
                            ->all();

                    $isUnique =
                        (int) ($rows->first()->Non_unique ?? 1)
                        === 0;

                    return
                        $columns === ['employee_id']
                        &&
                        ! $isUnique;
                }
            );

        if (! $hasNormalEmployeeIndex) {
            DB::statement(
                'ALTER TABLE `kabag_employee` '
                . 'ADD INDEX `kabag_employee_employee_id_index` (`employee_id`)'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 2. DROP UNIQUE LAMA employee_id
        |--------------------------------------------------------------------------
        */

        $indexes =
            collect(
                DB::select('SHOW INDEX FROM `kabag_employee`')
            )->groupBy('Key_name');

        foreach ($indexes as $keyName => $rows) {

            if ($keyName === 'PRIMARY') {
                continue;
            }

            $isUnique =
                (int) ($rows->first()->Non_unique ?? 1)
                === 0;

            $columns =
                $rows
                    ->sortBy('Seq_in_index')
                    ->pluck('Column_name')
                    ->values()
                    ->all();

            if (
                $isUnique
                &&
                $columns === ['employee_id']
            ) {
                DB::statement(
                    'ALTER TABLE `kabag_employee` '
                    . 'DROP INDEX `'
                    . str_replace('`', '``', $keyName)
                    . '`'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 3. TAMBAH UNIQUE KOMBINASI KABAG + EMPLOYEE
        |--------------------------------------------------------------------------
        */

        $indexes =
            collect(
                DB::select('SHOW INDEX FROM `kabag_employee`')
            )->groupBy('Key_name');

        $hasCompositeUnique =
            $indexes->contains(
                function ($rows) {

                    $isUnique =
                        (int) ($rows->first()->Non_unique ?? 1)
                        === 0;

                    $columns =
                        $rows
                            ->sortBy('Seq_in_index')
                            ->pluck('Column_name')
                            ->values()
                            ->all();

                    return
                        $isUnique
                        &&
                        $columns === [
                            'kabag_user_id',
                            'employee_id',
                        ];
                }
            );

        if (! $hasCompositeUnique) {
            DB::statement(
                'ALTER TABLE `kabag_employee` '
                . 'ADD UNIQUE `kabag_employee_kabag_user_employee_unique` '
                . '(`kabag_user_id`, `employee_id`)'
            );
        }
    }


    public function down(): void
    {
        if (! Schema::hasTable('kabag_employee')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DROP UNIQUE KOMBINASI
        |--------------------------------------------------------------------------
        */

        $indexes =
            collect(
                DB::select('SHOW INDEX FROM `kabag_employee`')
            )->groupBy('Key_name');

        if (
            $indexes->has(
                'kabag_employee_kabag_user_employee_unique'
            )
        ) {
            DB::statement(
                'ALTER TABLE `kabag_employee` '
                . 'DROP INDEX `kabag_employee_kabag_user_employee_unique`'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KEMBALIKAN UNIQUE employee_id
        |--------------------------------------------------------------------------
        |
        | Hanya akan berhasil jika belum ada employee yang punya > 1 Kabag.
        |
        */

        DB::statement(
            'ALTER TABLE `kabag_employee` '
            . 'ADD UNIQUE `kabag_employee_employee_id_unique` (`employee_id`)'
        );


        /*
        |--------------------------------------------------------------------------
        | DROP INDEX BIASA TAMBAHAN
        |--------------------------------------------------------------------------
        */

        $indexes =
            collect(
                DB::select('SHOW INDEX FROM `kabag_employee`')
            )->groupBy('Key_name');

        if (
            $indexes->has(
                'kabag_employee_employee_id_index'
            )
        ) {
            DB::statement(
                'ALTER TABLE `kabag_employee` '
                . 'DROP INDEX `kabag_employee_employee_id_index`'
            );
        }
    }
};
