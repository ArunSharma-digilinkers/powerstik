<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Ops;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function index(): View
    {
        return view('admin.system.index', [
            'info' => [
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'Environment' => app()->environment(),
                'Debug mode' => config('app.debug') ? 'ON — turn off in production' : 'off',
                'Pending jobs' => DB::table('jobs')->count(),
                'Failed jobs' => DB::table('failed_jobs')->count(),
                'Last cron run' => cache('ops.last_schedule_run', 'never'),
            ],
            'actions' => array_keys(Ops::ACTIONS),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        $action = $request->validate(['action' => ['required', Rule::in(array_keys(Ops::ACTIONS))]])['action'];

        return back()->with('ops_output', Ops::run($action));
    }
}
