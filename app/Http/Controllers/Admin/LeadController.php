<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.leads.index', [
            'leads' => $this->filtered($request)->with('assignee')->withCount('files')->paginate(30)->withQueryString(),
            'counts' => Lead::query()->where('status', 'new')->selectRaw('type, count(*) as n')->groupBy('type')->pluck('n', 'type'),
            'staff' => $this->staff(),
        ]);
    }

    public function show(Lead $lead): View
    {
        return view('admin.leads.show', ['lead' => $lead->load('files', 'assignee'), 'staff' => $this->staff()]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]));

        return back()->with('status', 'Lead updated.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->files->each->delete();
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }

    public function file(Lead $lead, LeadFile $file): StreamedResponse
    {
        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Date', 'Type', 'Status', 'Name', 'Company', 'Email', 'Phone', 'Country', 'City', 'Message', 'Details']);

            $query->chunk(500, function ($leads) use ($out) {
                foreach ($leads as $lead) {
                    fputcsv($out, [
                        $lead->id, $lead->created_at->format('Y-m-d H:i'), Lead::TYPES[$lead->type] ?? $lead->type,
                        Lead::STATUSES[$lead->status] ?? $lead->status, $lead->name, $lead->company, $lead->email,
                        $lead->phone, $lead->country, $lead->city, $lead->message, json_encode($lead->payload),
                    ]);
                }
            });

            fclose($out);
        }, 'powerstik-leads-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request): Builder
    {
        return Lead::query()
            ->when($request->query('type'), fn ($q, $v) => $q->where('type', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v), fn ($q) => $q->where('status', '!=', 'spam'))
            ->when($request->query('assigned'), fn ($q, $v) => $q->where('assigned_to', $v))
            ->when(trim((string) $request->query('q')), fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")))
            ->latest();
    }

    private function staff(): array
    {
        return User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }
}
