<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Contracts\View\View;

class PharmacyController extends Controller
{
    public function index(): View
    {
        return view('website.pharmacies.index', [
            'pharmacies' => Pharmacy::query()->get(['id', 'name', 'slug', 'description', 'phone', 'address', 'image']),
        ]);
    }

    public function show(Pharmacy $pharmacy): View
    {
        return view('website.pharmacies.show', [
            'pharmacy' => $pharmacy,
        ]);
    }
}
