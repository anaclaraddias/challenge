<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de jogadores</title>
    <!-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> -->
</head>

<body>
    <div>
        <header>
            <h1>Cadastro de jogador</h1>
        </header>

        <form action="{{ route('create-player') }}" method="post">
            @csrf
                <div>
                    <label for="player_name">Nome do jogador:</label>
                    <input name="player_name" type="text" required>
                </div>

                <div>
                    <label for="player_hability">Nível de Habilidade:</label>
                    <input name="player_hability" type="number" min="0" max="5" required>
                </div>

                <div>
                    <label for="is_goalkeeper">O jogador é goleiro?</label>
                    <label>
                        <input type="radio" name="is_goalkeeper" value="sim" required> Sim
                    </label>
                    <label>
                        <input type="radio" name="is_goalkeeper" value="nao" required> Não
                    </label>
                </div>
            </div>

            <button type="submit">Enviar</button>
        </form>

        <footer>
            Ana Clara Dias - Yetz
        </footer>
    </div>
</body>
</html>