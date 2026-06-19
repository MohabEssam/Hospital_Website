<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ServesResultFiles
{
    protected function serveResultFile(Request $request, ?array $paths, int $index): StreamedResponse
    {
        $path = $paths[$index] ?? null;
        abort_unless(is_string($path) && Storage::disk('public')->exists($path), 404);

        return $request->boolean('download')
            ? Storage::disk('public')->download($path)
            : Storage::disk('public')->response($path);
    }
}
