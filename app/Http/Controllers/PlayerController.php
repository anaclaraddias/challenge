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

    private array $confirmedPlayers;
    private array $teams = [[], []];
    private array $teamsInfo = [
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

    private int $teamIndex;
    private int $maxNumberOfPlayersWithHighestSkill;
    private int $teamHabilityMaxValue;
    private Player $player;

    public function setConfirmedPlayers(array $confirmedPlayers): void
    {
        $this->confirmedPlayers = $confirmedPlayers;
    }

    public function setTeams(array $teams): void
    {
        $this->teams = $teams;
    }

    public function setTeamsInfo(array $teamsInfo): void
    {
        $this->teamsInfo = $teamsInfo;
    }

    public function setTeamIndex(int $teamIndex): void
    {
        $this->teamIndex = $teamIndex;
    }

    public function setMaxNumberOfPlayersWithHighestSkill(int $maxNumberOfPlayersWithHighestSkill): void
    {
        $this->maxNumberOfPlayersWithHighestSkill = $maxNumberOfPlayersWithHighestSkill;
    }

    public function setTeamHabilityMaxValue(int $teamHabilityMaxValue): void
    {
        $this->teamHabilityMaxValue = $teamHabilityMaxValue;
    }


    public function setPlayer(Player $player): void
    {
        $this->player = $player;
    }

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

        if (count($confirmedPlayers) != $playersQuantity*2) {
            return response()->json(['error' => 'Escolha a quantidade correta de jogadores para a formação dos dois times com jogadores'], 400);
        }

        $this->setConfirmedPlayers($confirmedPlayers);

        $maxIterations = ($playersQuantity*2) + 5;
        $counter = 0;
        $this->setMaxNumberOfPlayersWithHighestSkill($this->calculateTeamBalancing($playersQuantity));
        $this->setTeamHabilityMaxValue($this->calculateTeamHabilityMaxValue($playersQuantity));

        $players = Player::whereIn('name', $this->confirmedPlayers)->get()->keyBy('name');

        while (!empty($this->confirmedPlayers)) {
            $this->setTeamIndex($counter % 2);

            if ($counter == $maxIterations){
                break;
            }

            if (count($this->teams[$this->teamIndex]) == $playersQuantity) {
                $counter++;
                continue;
            }

            $randomIndex = array_rand($this->confirmedPlayers);
            $playerName = $this->confirmedPlayers[$randomIndex];
            $this->setPlayer($players[$playerName]);

            if ($this->isPlayerAFitForTeam()) {
                $this->updateTeamInfo($this->teamsInfo[$this->teamIndex]);
                $this->addPlayerToTeam($this->player);

                unset($this->confirmedPlayers[$randomIndex]);
                $this->confirmedPlayers = array_values($this->confirmedPlayers);
            }

            $counter++;
        }

        if ($counter == $maxIterations) {
            foreach ($this->confirmedPlayers as $playerName) {
                $this->setTeamIndex($counter % 2);

                if (count($this->teams[$this->teamIndex]) == 5) {
                    $counter++;
                    $this->setTeamIndex($counter % 2);
                }

                $this->setPlayer($players[$playerName]);
                $this->updateTeamInfo($this->teamsInfo[$this->teamIndex]);
                $this->addPlayerToTeam($this->player);
                
                $counter++;
            }
        }

        session(['teams' => $this->teams, 'teamsInfo' => $this->teamsInfo]);

        return response()->json(['url' => "/teams"]);
    }

    

    private function calculateTeamBalancing(int $playersQuantity): int
    {
        return match ($playersQuantity) {
            1, 2 => 1,
            default => round($playersQuantity / 2) - 1,
        };
    }

    private function calculateTeamHabilityMaxValue(int $playersQuantity): int {
        return (
            $this->maxNumberOfPlayersWithHighestSkill*self::HABILITY_MAX_VALUE
        ) + $this->factorial($playersQuantity - $this->maxNumberOfPlayersWithHighestSkill);
    }

    private function factorial(int $number): int 
    {
        return ($number * ($number + 1)) / 2;
    }

    private function isPlayerAFitForTeam(): bool 
    {
        if (count($this->confirmedPlayers) == 1) {
            return true;
        }

        if ($this->shouldSkipBalancing()) {
            return true;
        }

        if ($this->player->hability + $this->teamsInfo[$this->teamIndex]["currentHabilityValue"] >= $this->teamHabilityMaxValue) {
            return false;
        }

        if (
            $this->player->hability === self::HABILITY_MAX_VALUE && 
            $this->teamsInfo[$this->teamIndex]["currentQuantityOfPlayersWithHighestSkill"] >= $this->maxNumberOfPlayersWithHighestSkill
        ) {
            return false;
        }

        if ($this->player->is_goalkeeper && $this->teamsInfo[$this->teamIndex]["hasGoalkeeper"]) {
            return false;
        }

        return true;
    }

    private function shouldSkipBalancing(): bool 
    {
        if (
            !$this->teamsInfo[0]["currentHabilityValue"] >= $this->teamHabilityMaxValue || 
            !$this->teamsInfo[1]["currentHabilityValue"] >= $this->teamHabilityMaxValue
        ) {
            return false;
        }

        if (
            $this->player->hability === self::HABILITY_MAX_VALUE && 
            !$this->teamsInfo[0]["currentQuantityOfPlayersWithHighestSkill"] >= $this->maxNumberOfPlayersWithHighestSkill ||
            !$this->teamsInfo[1]["currentQuantityOfPlayersWithHighestSkill"] >= $this->maxNumberOfPlayersWithHighestSkill
        ) {
            return false;
        }

        if (
            $this->player->is_goalkeeper && 
            !$this->teamsInfo[0]["hasGoalkeeper"] ||
            !$this->teamsInfo[1]["hasGoalkeeper"]
        ) {
            return false;
        }

        return true;
    }

    private function addPlayerToTeam(Player $player): void
    {
        $this->teams[$this->teamIndex][] = $player;
    }

    private function updateTeamInfo(array &$teamInfo): void 
    {
        if ($this->player->hability === self::HABILITY_MAX_VALUE) {
            $teamInfo["currentQuantityOfPlayersWithHighestSkill"]++;
        }

        if ($this->player->is_goalkeeper) {
            $teamInfo["hasGoalkeeper"] = true;
        }

        $teamInfo["currentHabilityValue"] += $this->player->hability;
    }

    public function listTeams(Request $request): View
    {
        $allData = session()->all();
        return view('teams', [
            'teams' => $allData['teams'], 
            'teamsInfo' => $allData['teamsInfo']
        ]);
    }
}
