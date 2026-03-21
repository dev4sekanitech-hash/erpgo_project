<?php

namespace App\Http\Controllers\Starrlight;

use App\Http\Controllers\Controller;
use App\Models\Starrlight\CareerApplication;
use Inertia\Inertia;

class CareerController extends Controller
{
    public function index()
    {
        $applications = CareerApplication::orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('starrlight/careers/index', [
            'careers' => $applications,
        ]);
    }

    public function show($id)
    {
        $application = CareerApplication::findOrFail($id);

        return Inertia::render('starrlight/careers/show', [
            'application' => $application,
        ]);
    }
}
