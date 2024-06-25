<?php

namespace Database\Seeders;

use App\Models\NavigationLink;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NavigationLinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $navLinks = [
            ['name' => 'Home', 'route' => '', 'icon' => 'assets/img/nav-bullet.png', 'is_navbar' => true],
            ['name' => 'About', 'route' => 'about', 'icon' => 'assets/img/nav-bullet.png', 'is_navbar' => true],
            ['name' => 'Servers', 'route' => 'servers', 'icon' => 'assets/img/nav-bullet.png', 'is_navbar' => true],
            ['name' => 'Pricing', 'route' => 'price', 'icon' => 'assets/img/nav-bullet.png', 'is_navbar' => true],
            ['name' => 'Contact Us', 'route' => 'contact', 'icon' => 'assets/img/nav-bullet.png', 'is_navbar' => true],
        ];

        foreach ($navLinks as $link) {
            NavigationLink::create($link);
        }
    }
}
