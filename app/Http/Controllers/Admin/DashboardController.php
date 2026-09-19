<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newLeads' => Lead::where('status', 'new')->count(),
            'leadsThisMonth' => Lead::where('status', '!=', 'spam')->where('created_at', '>=', now()->startOfMonth())->count(),
            'recent' => Lead::where('status', '!=', 'spam')->latest()->limit(8)->get(),
        ]);
    }
}
