<?php

namespace App\Http\Controllers\Api;

use App\Models\Pricing;
use App\Models\ServerType;
use App\Models\Testimonial;
use App\Models\NavigationLink;
use App\Http\Controllers\Controller;
use App\Models\Location;

class GeneralController extends Controller
{
    public function getNavigationLinks()
    {
        $navLinks = NavigationLink::all();
        return response()->json($navLinks);
    }

    public function getPricingOptions()
    {
        $now = now();
        $pricingOptions = [
            'hourly' => Pricing::where('period', 'hourly')
                ->where('valid_from', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->where('valid_until', '>=', $now)
                        ->orWhereNull('valid_until');
                })
                ->min('price'),
            'monthly' => Pricing::where('period', 'monthly')
                ->where('valid_from', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->where('valid_until', '>=', $now)
                        ->orWhereNull('valid_until');
                })
                ->min('price'),
            'yearly' => Pricing::where('period', 'yearly')
                ->where('valid_from', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->where('valid_until', '>=', $now)
                        ->orWhereNull('valid_until');
                })
                ->min('price'),
        ];

        $maxSpecs = [
            'cpu' => ServerType::max('cpu_cores'),
            'ram' => ServerType::max('ram'),
            'storage' => ServerType::max('storage'),
            'network_speed' => ServerType::max('network_speed'),
        ];
        return response()->json(['pricingOptions' => $pricingOptions, 'maxSpecs' => $maxSpecs]);
    }

    public function getTestimonials()
    {
        $testimonials = Testimonial::all();
        return response()->json($testimonials);
    }

    public function getLocations()
    {
        $locations = Location::all();
        return response()->json($locations);
    }
}
