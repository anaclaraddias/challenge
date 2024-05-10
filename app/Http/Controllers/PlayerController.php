<?php

namespace App\Http\Controllers;

use App\Infra\Uuid\UuidGenerator;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Termwind\Components\Dd;

class PlayerController extends Controller
{
    public const IS_GOALKEEPER = "sim";
    public const IS_NOT_GOALKEEPER = "nao";
    public const HABILITY_MAX_VALUE = 5;

    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'player_name' => 'required|string',
            'player_hability' => 'required|string',
            'is_goalkeeper' => 'required|in:sim,nao',
        ]);

        $is_goalkeeper = $this->defineIfPlayerIsGoalkeeper($request->is_goalkeeper);

        Player::create([
            'id' => (new UuidGenerator())->generateUuid(),
            'name' => $request->player_name,
            'hability' => (int) $request->player_hability,
            'is_goalkeeper' => $is_goalkeeper,
        ]);

        return redirect()->route('index')->with('success', 'Jogador criado com sucesso!');
    }

    private function defineIfPlayerIsGoalkeeper(string $value): bool
    {
        if ($value === self::IS_GOALKEEPER) {
            return true;
        }

        return false;
    }

    public function listAll(): View
    {
        $players = Player::all();

        return view('playersPresence', ['players' => $players]);
    }

    public function generateTeam(Request $request)
    {
        $confirmedPlayers = $request->input('players');
        $playersQuantity = (int) $request->input('players_quantity');

        if (empty($confirmedPlayers)) {
            return response()->json(['error' => 'Os jogadores devem ser selecionados'], 400);
        }

        if (count($confirmedPlayers) < $playersQuantity*2) {
            return response()->json(['error' => 'Os jogadores confirmados não são suficientes para criar os times com a quantidade escolhida'], 400);
        }

        session([
            'confirmed_players' => $confirmedPlayers, 
            'players_quantity' => $playersQuantity
        ]);

        return response()->json(['url' => "/teams"]);
    }

    public function listTeams(Request $request): View
    {
        $confirmedPlayers = $request->session()->get('confirmed_players', []);
        $playersQuantity = (int) $request->session()->get('players_quantity');

        $teams = [[], []];
        $counter = 0;
        $maxNumberOfPlayersWithHighestSkill = $this->calculateTeamBalancing($playersQuantity);
        $teamHabilityMaxValue = $this->calculateTeamHabilityMaxValue($playersQuantity, $maxNumberOfPlayersWithHighestSkill);

        $teamsInfo = [
            [
                "currentHabilityValue" => 0,
                "currentQuantityOfPlayersWithHighestSkill" => 0,
                "hasGoalkeeper" => false,
            ], 
            [
                "currentHabilityValue" => 0,
                "currentQuantityOfPlayersWithHighestSkill" => 0,
                "hasGoalkeeper" => false,
            ]
        ];

        $loopCondition = count($teams[0]) != $playersQuantity && count($teams[1]) != $playersQuantity;

        while ($loopCondition) {
            $teamIndex = $counter % 2;
            $team = $teams[$teamIndex];

            if (count($team) == $playersQuantity) {
                $counter++;
                break;
            }

            $randomIndex = rand(0, count($confirmedPlayers) - 1);
            $playerName = $confirmedPlayers[$randomIndex];
            $player = Player::where('name', $playerName)->first();

            if (
                !$this->isPlayerAFitForTeam(
                    $player,
                    $maxNumberOfPlayersWithHighestSkill,
                    $teamHabilityMaxValue,
                    $teamsInfo[$teamIndex]
                )
            ) {
                $counter++;
                continue;
            }

            $this->updateTeamInfo($teamsInfo[$teamIndex], $player);
            $this->updateTeamVariables(
                $teams,
                $teamIndex,
                $player,
                $randomIndex,
                $confirmedPlayers,
                $counter
            );
        }

        dd($teamsInfo);

        return view('teams');
    }

    private function calculateTeamBalancing(int $playersQuantity): int
    {
        return match ($playersQuantity) {
            1, 2 => 1,
            default => round($playersQuantity / 2) - 1,
        };
    }

    private function calculateTeamHabilityMaxValue(
        int $playersQuantity, 
        int $maxNumberOfPlayersWithHighestSkill
    ): int {
        return ($maxNumberOfPlayersWithHighestSkill*self::HABILITY_MAX_VALUE) + $this->factorial($playersQuantity - $maxNumberOfPlayersWithHighestSkill);
    }

    private function factorial(int $number): int 
    {
        $factorial = 0;

        for ($i = 1; $i <= $number; $i++) {
            $factorial += $i;
        }

        return $factorial;
    }

    private function isPlayerAFitForTeam(
        Player $player,
        int $maxNumberOfPlayersWithHighestSkill,
        int $teamHabilityMaxValue,
        array $teamInfo
    ): bool {
        if ($player->hability + $teamInfo["currentHabilityValue"] >= $teamHabilityMaxValue) {
            return false;
        }

        if (
            $player->hability === self::HABILITY_MAX_VALUE && 
            $teamInfo["currentQuantityOfPlayersWithHighestSkill"] >= $maxNumberOfPlayersWithHighestSkill
        ) {
            return false;
        }

        if ($player->is_goalkeeper && $teamInfo["hasGoalkeeper"]) {
            return false;
        }

        return true;
    }

    private function updateTeamInfo(array &$teamInfo, Player $player): void 
    {
        if ($player->hability === self::HABILITY_MAX_VALUE) {
            $teamInfo["currentQuantityOfPlayersWithHighestSkill"]++;
        }

        if ($player->is_goalkeeper) {
            $teamInfo["hasGoalkeeper"] = true;
        }

        $teamInfo["currentHabilityValue"] = $player->hability + $teamInfo["currentHabilityValue"];
    }

    private function updateTeamVariables(
        array &$teams,
        int $teamIndex,
        Player $player,
        int $randomIndex,
        array &$confirmedPlayers,
        int &$counter
    ): void {
        $teams[$teamIndex][] = $player;
        unset($confirmedPlayers[$randomIndex]);
        $confirmedPlayers = array_values($confirmedPlayers);
        $counter++;
    }
}
