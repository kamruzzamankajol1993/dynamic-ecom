<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroRightSlider;

class HeroRightSliderSeeder extends Seeder
{
    public function run(): void
    {
        HeroRightSlider::updateOrCreate(['position' => 'top']);
        HeroRightSlider::updateOrCreate(['position' => 'bottom_left']);
        HeroRightSlider::updateOrCreate(['position' => 'bottom_right']);
    }
}