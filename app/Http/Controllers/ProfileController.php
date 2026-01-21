<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ReputationVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $topicsCount = $user->topics()->count();
        $postsCount = $user->posts()->count();
        $recentTopics = $user->topics()->latest()->take(5)->get();
        $recentPosts = $user->posts()->with('topic')->latest()->take(5)->get();

        // Check if current user has voted for this user
        $existingVote = null;
        if (Auth::check()) {
            $existingVote = ReputationVote::where('voter_id', Auth::id())
                ->where('user_id', $user->id)
                ->first();
        }

        return view('profile.show', compact('user', 'topicsCount', 'postsCount', 'recentTopics', 'recentPosts', 'existingVote'));
    }

    public function vote(Request $request, User $user)
    {
        // Cannot vote for yourself
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot vote for yourself.');
        }

        $request->validate([
            'vote' => 'required|in:1,-1',
        ]);

        $vote = (int) $request->vote;

        // Check if user already voted
        $existingVote = ReputationVote::where('voter_id', Auth::id())
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            // If same vote, remove it (toggle off)
            if ($existingVote->vote === $vote) {
                $user->decrement('reputation', $vote);
                $existingVote->delete();
                return back()->with('success', 'Vote removed.');
            }

            // If different vote, change it
            $user->decrement('reputation', $existingVote->vote);
            $user->increment('reputation', $vote);
            $existingVote->update(['vote' => $vote]);
            return back()->with('success', 'Vote changed.');
        }

        // Create new vote
        ReputationVote::create([
            'voter_id' => Auth::id(),
            'user_id' => $user->id,
            'vote' => $vote,
        ]);

        $user->increment('reputation', $vote);

        return back()->with('success', $vote === 1 ? 'Reputation increased!' : 'Reputation decreased.');
    }
}

