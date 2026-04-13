@extends("admin.layout")

@section("content")
    <h2 class="text-2xl font-medium text-black dark:text-white mb-6">Admin Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
            <p class="text-3xl font-medium text-black dark:text-white">{{ $stats["total_users"] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Sets</p>
            <p class="text-3xl font-medium text-black dark:text-white">{{ $stats["total_sets"] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Flashcards</p>
            <p class="text-3xl font-medium text-black dark:text-white">{{ $stats["total_flashcards"] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Generations</p>
            <p class="text-3xl font-medium text-black dark:text-white">{{ $stats["total_generations"] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-medium text-black dark:text-white mb-4">Activity Chart</h3>
            <canvas id="activityChart" height="200"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-medium text-black dark:text-white mb-4">Recent Activity</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800">
                        <div>
                            <p class="text-sm font-medium text-black dark:text-white">
                                {{ $activity->user->name ?? "Guest" }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity->action }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById("activityChart").getContext("2d");
        const dailyStats = @json($dailyStats);
        const labels = Object.keys(dailyStats).slice(0, 14).reverse();
        const generateData = labels.map(date => {
            const day = dailyStats[date].find(s => s.action === "generate");
            return day ? day.count : 0;
        });
        const saveData = labels.map(date => {
            const day = dailyStats[date].find(s => s.action === "save_set");
            return day ? day.count : 0;
        });

        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Generations",
                        data: generateData,
                        borderColor: "#0000FF",
                        backgroundColor: "rgba(12, 0, 128, 0.5)",
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: "Saved Sets",
                        data: saveData,
                        borderColor: "#6b7280",
                        backgroundColor: "rgba(107, 114, 128, 0.1)",
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: "#9ca3af" }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: "#374151" },
                        ticks: { color: "#9ca3af" }
                    },
                    x: {
                        grid: { color: "#374151" },
                        ticks: { color: "#9ca3af" }
                    }
                }
            }
        });
    </script>
@endsection
