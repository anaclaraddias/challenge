<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <!-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div>
        <header>
            <h1>Confirmar presença</h1>
        </header>

        <main>
            @foreach($players as $player)
                <label for="{{ $player->name }}">{{ $player->name }}</label>
                <label>
                    <input type="radio" name="{{ $player->name }}" value="sim" required> Sim
                </label>
                <label>
                    <input type="radio" name="{{ $player->name }}" value="nao" required> Não
                </label>
                <br>
            @endforeach

            <div>
                <label for="players_quantity">Número de jogadores por equipe:</label>
                <input name="players_quantity" type="number" min=2 required>
            </div>

            <button onclick="generateTeam()">Gerar times</button>
        </main>

        <footer>
            Ana Clara Dias - Yetz
        </footer>
    </div>
</body>
</html>

<script>


let players = [];

document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (radio.value === "sim") {
            players.push(radio.name);
            return
        } 

        players = players.filter(name => name !== radio.name);
    });
});

function generateTeam() {
    let playersQuantity = document.querySelector('input[name="players_quantity"]').value;

    fetch('/generate-team', {
        method: 'POST',
        async: false,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ players: players, players_quantity: playersQuantity})
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error);
            });
        }

        return response.json();
    })
    .then(data => {
        if (data.url) {
            window.location.href = data.url;
        } else {
            throw new Error("redirect url not found");
        }
    })
    .catch(error => {
        alert(error.message);
    });
}

</script>