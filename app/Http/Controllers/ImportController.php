<?php

namespace App\Http\Controllers;

use App\Enums\LeadSource;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    /**
     * Import people or leads from a CSV file with a header row.
     */
    public function store(Request $request, string $type): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = collect(fgetcsv($handle) ?: [])->map(fn ($column): string => Str::of((string) $column)->lower()->trim()->replace(' ', '_')->toString());

        if (! $header->contains('name') && ! $header->contains('title')) {
            fclose($handle);

            return back()->withErrors(['file' => 'The CSV needs a header row with at least a "name" column.']);
        }

        $imported = 0;
        $userId = $request->user()->id;

        DB::transaction(function () use ($handle, $header, $type, $userId, &$imported): void {
            while (($row = fgetcsv($handle)) !== false && $imported < 1000) {
                if (count($row) !== $header->count()) {
                    continue;
                }

                $data = $header->combine($row)->map(fn ($value): string => trim((string) $value));
                $name = $data->get('name') ?: $data->get('title');

                if ($name === null || $name === '') {
                    continue;
                }

                $organizationId = filled($data->get('organization'))
                    ? Organization::firstOrCreate(['name' => Str::limit($data->get('organization'), 250, ''), 'user_id' => $userId])->id
                    : null;

                if ($type === 'people') {
                    Person::create([
                        'name' => Str::limit($name, 250, ''),
                        'email' => filter_var($data->get('email'), FILTER_VALIDATE_EMAIL) ?: null,
                        'phone' => Str::limit((string) $data->get('phone'), 50, '') ?: null,
                        'job_title' => Str::limit((string) $data->get('job_title'), 250, '') ?: null,
                        'organization_id' => $organizationId,
                    ]);
                } else {
                    Lead::create([
                        'title' => Str::limit($name, 250, ''),
                        'value' => is_numeric($data->get('value')) ? $data->get('value') : null,
                        'organization_id' => $organizationId,
                        'source' => LeadSource::Import,
                    ]);
                }

                $imported++;
            }
        });

        fclose($handle);

        return back()->with('status', "Imported {$imported} ".($type === 'people' ? Str::plural('person', $imported) : Str::plural('lead', $imported)).'.');
    }
}
