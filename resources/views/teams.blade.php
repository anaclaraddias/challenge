<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div>
        <header class="d-flex justify-content-center py-3">
            <h1>Times</h1>
        </header>

        <main class="container">
            @foreach($teams as $index => $team)
                <div class="container">
                    <h3 class="d-flex justify-content-center py-3">Time {{ $index+1 }}:</h3>

                    @foreach($team as $player)
                        <p class="d-flex justify-content-center py-3">{{ $player->name }} - Habilidade: {{ $player->hability }} {{ $player->is_goalkeeper ? "- Goleiro" : "" }}</p>
                    @endforeach

                    <p class="d-flex justify-content-center py-3"><b>Total de habilidade do time: {{ $teamsInfo[$index]['currentHabilityValue'] }}</b></p>
                </div>
            @endforeach

            <div class="d-flex justify-content-center py-3">
                <a href="/"><button class="btn btn-secondary d-inline-flex align-items-center">Inicio</button></a>
            </div>
        </main>

        <footer class="d-flex justify-content-center py-3 border-top">
            Ana Clara Dias - Yetz
        </footer>
    </div>
</body>
</html>