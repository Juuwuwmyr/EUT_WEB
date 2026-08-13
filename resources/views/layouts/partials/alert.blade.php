@props([
    'title' => 'Message',
])

@if (session('status'))
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md" role="alert">
    <strong>{{ $title }}:</strong>
    <span class="ml-2">{{ session('status') }}</span>
</div>
@endif

@if ($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md" role="alert">
    <strong>{{ $title }}:</strong>
    <ul class="mt-2 list-disc list-inside">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif