<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashcardSet;
use App\Models\UsageLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_sets' => FlashcardSet::count(),
            'total_flashcards' => DB::table('flashcards')->count(),
            'total_generations' => UsageLog::where('action', 'generate')->count(),
        ];

        $recentActivity = UsageLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $dailyStats = UsageLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            'action'
        )
            ->groupBy('date', 'action')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->groupBy('date');

        return view('admin.dashboard', compact('stats', 'recentActivity', 'dailyStats'));
    }

    public function users()
    {
        $users = User::withCount(['flashcardSets', 'usageLogs'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.users', compact('users'));
    }

    public function userDetails(User $user)
    {
        $user->load(['flashcardSets.flashcards', 'tags']);

        $usageStats = UsageLog::where('user_id', $user->id)
            ->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->get();

        return view('admin.user-details', compact('user', 'usageStats'));
    }

    public function sets()
    {
        $sets = FlashcardSet::with(['user', 'flashcards'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.sets', compact('sets'));
    }

    public function setDetails(FlashcardSet $set)
    {
        $set->load(['user', 'flashcards', 'tags']);

        return view('admin.set-details', compact('set'));
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function deleteSet(FlashcardSet $set)
    {
        $set->delete();

        return redirect()->route('admin.sets')->with('success', 'Flashcard set deleted successfully.');
    }
}
