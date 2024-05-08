<?php

namespace App\Http\Controllers;

use App\Domain\Entity\Player;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PlayerController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'player_name' => 'required|string',
            'player_hability' => 'required|string',
            'is_goalkeeper' => 'required|in:sim,nao',
        ]);

        // Player::create([
        //     'name' => $request->nome,
        //     'hability' => $request->nivel,
        //     'is_goalkeeper' => $request->resposta,
        // ]);

        return redirect()->route('index')->with('success', 'Jogador criado com sucesso!');
    }
}
