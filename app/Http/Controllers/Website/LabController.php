<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use Illuminate\Contracts\View\View;

class LabController extends Controller
{
    public function index(): View
    {
        return view('website.labs.index', [
            'labs' => Lab::query()->get(['id', 'name', 'slug', 'description', 'phone', 'address', 'work_hours', 'image']),
        ]);
    }

    public function show(Lab $lab): View
    {
        return view('website.labs.show', [
            'lab' => $lab,
        ]);
    }
}
