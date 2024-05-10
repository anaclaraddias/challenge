<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        label{
            margin: 0 10px;
        }

        input{
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div>
        <header class="d-flex justify-content-center py-3">
            <h1>Cadastro de jogador</h1>
        </header>

        <main>
            <form action="{{ route('create-player') }}" method="post">
                @csrf
                    <div class="d-flex justify-content-center py-3">
                        <label for="player_name">Nome do jogador:</label>
                        <input name="player_name" type="text" required>
                    </div>

                    <div class="d-flex justify-content-center py-3">
                        <label for="player_hability">Nível de Habilidade:</label>
                        <input name="player_hability" type="number" min="0" max="5" required>
                    </div>

                    <div class="d-flex justify-content-center py-3">
                        <label for="is_goalkeeper">O jogador é goleiro?</label>
                        <label>
                            <input type="radio" name="is_goalkeeper" value="sim" required> Sim
                        </label>
                        <label>
                            <input type="radio" name="is_goalkeeper" value="nao" required> Não
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-center py-3">
                    <button type="submit" class="btn btn-success d-inline-flex align-items-center">Enviar</button>
                </div>
            </form>

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