<x-modal :name="'import-'.$type" :title="$type === 'people' ? 'Import contacts' : 'Import leads'">
    <form method="post" action="{{ route('import', $type) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_modal" value="import-{{ $type }}">
        <div class="space-y-4 px-5 py-4">
            <p class="text-sm text-slate-600">
                Upload a CSV file with a header row. Supported columns:
                <code class="rounded bg-slate-100 px-1 text-xs">{{ $type === 'people' ? 'name, email, phone, organization, job_title' : 'title, value, organization' }}</code>.
            </p>
            <div>
                <input type="file" name="file" accept=".csv,text/csv" required class="block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end gap-2 rounded-b-xl border-t border-slate-200 bg-slate-50 px-5 py-3">
            <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
            <button type="submit" class="btn-primary"><x-icon name="upload" /> Import</button>
        </div>
    </form>
</x-modal>
