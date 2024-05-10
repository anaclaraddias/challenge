<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        button{
            margin: 10px;
        }

        label{
            margin: 0 10px;
        }

        input{
            border-radius: 5px;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div>
        <header class="d-flex justify-content-center py-3">
            <h1>Confirmar presença</h1>
        </header>

        <main>
            <div class="container">
                @foreach($players as $player)
                    <div class="d-flex justify-content-center py-3">
                        <p for="{{ $player->name }}"><b>{{ $player->name }}</b></p>

                        <label>
                            <input type="radio" name="{{ $player->name }}" value="sim" required> Sim
                        </label>

                        <label>
                            <input type="radio" name="{{ $player->name }}" value="nao" required> Não
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center py-3">
                <label for="players_quantity">Número de jogadores por equipe:</label>
                <input name="players_quantity" type="number" min=2 required>
            </div>

            <div class="d-flex justify-content-center py-3">
                <button class="btn btn-primary d-inline-flex align-items-center" onclick="generateTeam()">Gerar times</button>
                <a href="/"><button class="btn btn-secondary d-inline-flex align-items-center">Inicio</button></a>
            </div>
        </main>

        <footer class="d-flex justify-content-center py-3 border-top">
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