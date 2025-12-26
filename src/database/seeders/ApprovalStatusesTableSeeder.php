<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalStatus;

class ApprovalStatusesTableSeeder extends Seeder
{
    public function run()
    {
        $statuses = ['承認待ち', '承認済み', '却下'];

        foreach ($statuses as $status) {
            ApprovalStatus::firstOrCreate(['name' => $status]);
        }
    }
}