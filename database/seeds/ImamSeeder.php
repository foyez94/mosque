<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('imams')->insert([
            [
                'name' => 'MD. Masum Billah',
                'image_id' => '1',
            ],
        ]);
    }
}