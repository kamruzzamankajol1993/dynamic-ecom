<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FooterBanner;

class FooterBannerSeeder extends Seeder
{
    public function run(): void
    {
        // This will create the single row if it doesn't exist
        FooterBanner::updateOrCreate(['id' => 1]);
    }
}