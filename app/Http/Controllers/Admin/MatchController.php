<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasketballMatch;
use App\Models\Standing;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $matches = BasketballMatch::orderBy('match_date', 'asc')->paginate(20);
        $standing = Standing::first() ?? new Standing();
        return view('admin.matches.index', compact('matches', 'standing'));
    }

    public function create()
    {
        return view('admin.matches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'round' => 'nullable|integer',
            'match_date' => 'nullable|date',
            'home_team' => 'required|string|max:150',
            'away_team' => 'required|string|max:150',
            'home_score' => 'nullable|integer',
            'away_score' => 'nullable|integer',
            'venue' => 'nullable|string|max:150',
            'status' => 'required|in:upcoming,played,postponed',
            'is_home' => 'nullable|boolean',
            'is_next_match' => 'nullable|boolean',
        ]);

        $data['is_home'] = $request->has('is_home');
        $data['is_next_match'] = $request->has('is_next_match');

        if ($data['is_next_match']) {
            BasketballMatch::where('id', '>', 0)->update(['is_next_match' => false]);
        }

        BasketballMatch::create($data);

        return redirect()->route('admin.matches.index')->with('success', 'Karşılaşma başarıyla eklendi.');
    }

    public function edit(BasketballMatch $match)
    {
        return view('admin.matches.edit', compact('match'));
    }

    public function update(Request $request, BasketballMatch $match)
    {
        $data = $request->validate([
            'round' => 'nullable|integer',
            'match_date' => 'nullable|date',
            'home_team' => 'required|string|max:150',
            'away_team' => 'required|string|max:150',
            'home_score' => 'nullable|integer',
            'away_score' => 'nullable|integer',
            'venue' => 'nullable|string|max:150',
            'status' => 'required|in:upcoming,played,postponed',
            'is_home' => 'nullable|boolean',
            'is_next_match' => 'nullable|boolean',
        ]);

        $data['is_home'] = $request->has('is_home');
        $data['is_next_match'] = $request->has('is_next_match');

        if ($data['is_next_match']) {
            BasketballMatch::where('id', '!=', $match->id)->update(['is_next_match' => false]);
        }

        $match->update($data);

        return redirect()->route('admin.matches.index')->with('success', 'Karşılaşma güncellendi.');
    }

    public function updateStanding(Request $request)
    {
        $data = $request->validate([
            'rank' => 'required|integer',
            'played' => 'required|integer',
            'won' => 'required|integer',
            'lost' => 'required|integer',
            'points' => 'required|integer',
            'points_for' => 'required|integer',
            'points_against' => 'required|integer',
        ]);

        $standing = Standing::first() ?? new Standing();
        $standing->fill($data);
        $standing->save();

        return redirect()->route('admin.matches.index')->with('success', 'Puan durumu / sezon özeti başarıyla güncellendi.');
    }

    public function destroy(BasketballMatch $match)
    {
        $match->delete();
        return redirect()->route('admin.matches.index')->with('success', 'Karşılaşma silindi.');
    }
}
