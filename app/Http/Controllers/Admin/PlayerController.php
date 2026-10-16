<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('stats')->orderBy('order', 'asc')->paginate(20);
        return view('admin.players.index', compact('players'));
    }

    public function create()
    {
        return view('admin.players.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'jersey_number' => 'nullable|string|max:10',
            'position' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'birth_date' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
            // Stats
            'games_played' => 'nullable|integer',
            'minutes_avg' => 'nullable|numeric',
            'points_avg' => 'nullable|numeric',
            'rebounds_avg' => 'nullable|numeric',
            'assists_avg' => 'nullable|numeric',
            'steals_avg' => 'nullable|numeric',
            'blocks_avg' => 'nullable|numeric',
            'efficiency_avg' => 'nullable|numeric',
            'fg_percentage' => 'nullable|string',
            'three_point_percentage' => 'nullable|string',
            'ft_percentage' => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? (Player::max('order') + 1);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'player_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/players'), $filename);
            $data['image_url'] = 'assets/players/' . $filename;
        }

        $player = Player::create($data);

        // Create player stats
        PlayerStat::create([
            'player_id' => $player->id,
            'season' => '2026-2027',
            'games_played' => $request->input('games_played', 0),
            'minutes_avg' => $request->input('minutes_avg', 0),
            'points_avg' => $request->input('points_avg', 0),
            'rebounds_avg' => $request->input('rebounds_avg', 0),
            'assists_avg' => $request->input('assists_avg', 0),
            'steals_avg' => $request->input('steals_avg', 0),
            'blocks_avg' => $request->input('blocks_avg', 0),
            'efficiency_avg' => $request->input('efficiency_avg', 0),
            'fg_percentage' => $request->input('fg_percentage', '0%'),
            'three_point_percentage' => $request->input('three_point_percentage', '0%'),
            'ft_percentage' => $request->input('ft_percentage', '0%'),
            'total_points' => (int) round($request->input('points_avg', 0) * $request->input('games_played', 0)),
            'total_rebounds' => (int) round($request->input('rebounds_avg', 0) * $request->input('games_played', 0)),
            'total_assists' => (int) round($request->input('assists_avg', 0) * $request->input('games_played', 0)),
        ]);

        return redirect()->route('admin.players.index')->with('success', 'Oyuncu başarıyla eklendi.');
    }

    public function edit(Player $player)
    {
        $player->load('stats');
        return view('admin.players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'jersey_number' => 'nullable|string|max:10',
            'position' => 'nullable|string|max:50',
            'height' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'birth_date' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
            // Stats
            'games_played' => 'nullable|integer',
            'minutes_avg' => 'nullable|numeric',
            'points_avg' => 'nullable|numeric',
            'rebounds_avg' => 'nullable|numeric',
            'assists_avg' => 'nullable|numeric',
            'steals_avg' => 'nullable|numeric',
            'blocks_avg' => 'nullable|numeric',
            'efficiency_avg' => 'nullable|numeric',
            'fg_percentage' => 'nullable|string',
            'three_point_percentage' => 'nullable|string',
            'ft_percentage' => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'player_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/players'), $filename);
            $data['image_url'] = 'assets/players/' . $filename;
        }

        $player->update($data);

        // Update or create stats
        $gp = (int) $request->input('games_played', 0);
        $pts = (float) $request->input('points_avg', 0);
        $reb = (float) $request->input('rebounds_avg', 0);
        $ast = (float) $request->input('assists_avg', 0);

        PlayerStat::updateOrCreate(
            ['player_id' => $player->id, 'season' => '2026-2027'],
            [
                'games_played' => $gp,
                'minutes_avg' => $request->input('minutes_avg', 0),
                'points_avg' => $pts,
                'rebounds_avg' => $reb,
                'assists_avg' => $ast,
                'steals_avg' => $request->input('steals_avg', 0),
                'blocks_avg' => $request->input('blocks_avg', 0),
                'efficiency_avg' => $request->input('efficiency_avg', 0),
                'fg_percentage' => $request->input('fg_percentage', '0%'),
                'three_point_percentage' => $request->input('three_point_percentage', '0%'),
                'ft_percentage' => $request->input('ft_percentage', '0%'),
                'total_points' => (int) round($pts * $gp),
                'total_rebounds' => (int) round($reb * $gp),
                'total_assists' => (int) round($ast * $gp),
            ]
        );

        return redirect()->route('admin.players.index')->with('success', 'Oyuncu ve istatistikleri başarıyla güncellendi.');
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('admin.players.index')->with('success', 'Oyuncu silindi.');
    }
}
