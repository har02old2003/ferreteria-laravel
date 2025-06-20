<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FerreteriaDatosSeeder extends Seeder
{
    public function run()
    {
        $sql = File::get(database_path('seeders/sql/ferreteria_datos.sql'));
        DB::unprepared($sql);
    }
} 