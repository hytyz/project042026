@extends("admin.layout")

@section("content")
    <h2 class="text-2xl font-medium text-black dark:text-white mb-6">Flashcard Sets</h2>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cards</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @foreach($sets as $set)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black dark:text-white">{{ $set->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $set->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $set->flashcards->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $set->created_at->format("M d, Y") }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route("admin.sets.show", $set) }}" class="text-black dark:text-white hover:underline">View</a>
                            <form action="{{ route("admin.sets.destroy", $set) }}" method="POST" class="inline ml-3" onsubmit="return confirm("Delete this set?")">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sets->links() }}
    </div>
@endsection
