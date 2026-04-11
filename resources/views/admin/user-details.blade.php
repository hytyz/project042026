@extends("admin.layout")

@section("content")
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Details: {{ $user->name }}</h2>
        <a href="{{ route("admin.users") }}" class="text-primary-600 dark:text-primary-400 hover:underline">Back to Users</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Info</h3>
                <div class="space-y-2">
                    <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Name:</span> <span class="text-gray-900 dark:text-white">{{ $user->name }}</span></p>
                    <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="text-gray-900 dark:text-white">{{ $user->email }}</span></p>
                    <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Joined:</span> <span class="text-gray-900 dark:text-white">{{ $user->created_at->format("F j, Y") }}</span></p>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 mt-6">Usage Stats</h3>
                <div class="space-y-2">
                    @foreach($usageStats as $stat)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">{{ $stat->action }}</span>
                            <span class="text-gray-900 dark:text-white">{{ $stat->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Flashcard Sets ({{ $user->flashcardSets->count() }})</h3>
                <div class="space-y-4">
                    @forelse($user->flashcardSets as $set)
                        <div class="border dark:border-gray-600 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $set->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $set->flashcards->count() }} cards</p>
                                    <div class="flex gap-2 mt-2">
                                        @foreach($set->tags as $tag)
                                            <span class="px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $set->created_at->format("M d, Y") }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">No flashcard sets yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
