@extends("admin.layout")

@section("content")
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Set: {{ $set->title }}</h2>
        <a href="{{ route("admin.sets") }}" class="text-primary-600 dark:text-primary-400 hover:underline">Back to Sets</a>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Owner</p>
                <p class="text-gray-900 dark:text-white">{{ $set->user->name }} ({{ $set->user->email }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                <p class="text-gray-900 dark:text-white">{{ $set->created_at->format("F j, Y g:i A") }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tags</p>
                <div class="flex gap-2">
                    @forelse($set->tags as $tag)
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $tag->name }}</span>
                    @empty
                        <span class="text-gray-400">No tags</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Flashcards ({{ $set->flashcards->count() }})</h3>
    <div class="space-y-4">
        @foreach($set->flashcards as $card)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Question {{ $loop->iteration }}</p>
                <p class="text-gray-900 dark:text-white font-medium mb-3">{{ $card->question }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Answer</p>
                <p class="text-gray-700 dark:text-gray-300">{{ $card->answer }}</p>
            </div>
        @endforeach
    </div>
@endsection