<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Contracts\View\View;

class HospitalController extends Controller
{
    public function index(): View
    {
        return view('website.hospitals.index', [
            'hospitals' => Hospital::query()->get(['id', 'name', 'slug', 'description', 'phone', 'address', 'image']),
        ]);
    }

    public function show(Hospital $hospital): View
    {
        return view('website.hospitals.show', [
            'hospital' => $hospital,
        ]);
    }
}
