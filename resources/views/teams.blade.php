<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <!-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> -->
</head>

@php
dd(session('confirmed_players'));
@endphp

<body>
    <div>
        <header>
            <h1>Times</h1>
        </header>

        <main>
            @foreach($teams as $team)
                <label for="{{ $player->name }}">{{ $team->name }}</label>
                <label>
                    <input type="radio" name="{{ $player->name }}" value="sim" required> Sim
                </label>
                <label>
                    <input type="radio" name="{{ $player->name }}" value="nao" required> Não
                </label>

            @endforeach
        </main>

        <footer>
            Ana Clara Dias - Yetz
        </footer>
    </div>
</body>
</html>