<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\History;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $newThing = $request->only(["played_at", "classement"]);

        // Classement is an array of [playerSlug, outBy?, position]
        $classement = $newThing['classement'];
        // Classement will be stored in History models, not directly in Game
        unset($newThing['classement']);

        $newThing['nbPlayers'] = count($classement);

        DB::beginTransaction();
        
        try {
            $game = Game::create($newThing);

            foreach($newThing['players'] as $player) {
                $updatedPlayer = User::where('slug', $player['playerSlug'])->firstOrFail();

                $updatedPlayer->nbSessions++;

                // Update points related fields
                $newPoints = $this->evaluatePoints($player['position'], $newThing['nbPlayers']);
                $updatedPlayer->points += $newPoints;
                $updatedPlayer->efficiency = $updatedPlayer->points / $updatedPlayer->nbSessions;

                // Update eliminations related fields
                $newKills = $this->evaluateKills($classement, $updatedPlayer->slug);
                $updatedPlayer->kills += $newKills;
                $updatedPlayer->averageKills = $updatedPlayer->kills / $updatedPlayer->nbSessions;

                // First position has not been killed, therefore will not have outBy field
                $killer = ($player['outBy']) ? User::where('slug', $player['outBy'])->firstOrFail()->id : null;
                
                // Insert game history with stats for each player
                History::create([
                    "player_id" => $updatedPlayer->id,
                    "game_id" => $game->id,
                    "points" => $newPoints,
                    "kills" => $newKills,
                    "outBy" => $killer,
                ]);

                $updatedPlayer->save();
            }
        } catch(Exception $e) {
            throw $e;
        }
        
        DB::commit();

        return response()->json($game->load('history'));
    }

    private function evaluatePoints(int $position, int $nbPlayers): int
    {
        $scoreElement = $position / $nbPlayers;
        $score = 1 - $scoreElement;

        $coeffElement = $nbPlayers / $position;
        $coeffElement = $coeffElement**(1/3);

        return $score * $coeffElement * 100;
    }

    private function evaluateKills(array $classement, string $playerSlug): int
    {
        $kills = array_filter($classement, function() use($playerSlug)
        {
            return $classement['outBy'] = $playerSlug;
        });

        return count($kills);
    }
}
