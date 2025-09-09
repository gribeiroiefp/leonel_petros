<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_livro = (int) ($_POST['id_livro'] ?? 0);
    $autores = $_POST['autores'] ?? [];
    $papel = mysqli_real_escape_string($conn, $_POST['papel'] ?? '');

    if ($id_livro > 0 && !empty($autores) && $papel !== '') {
        foreach ($autores as $id_autor) {
            $id_autor = (int) $id_autor;
            $check_sql = "SELECT * FROM livro_autor WHERE id_livro = $id_livro AND id_autor = $id_autor";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) === 0) {
                $sql = "INSERT INTO livro_autor (id_livro, id_autor, papel) VALUES ($id_livro, $id_autor, '$papel')";
                mysqli_query($conn, $sql);
            }
        }
        $mensagem = "Autores associados ao livro com sucesso!";
    } else {
        $mensagem = "Por favor, selecione um livro, pelo menos um autor e informe o papel.";
    }
}

// Busca livros para dropdown
$resultado_livros = mysqli_query($conn, "SELECT id, titulo FROM livros ORDER BY titulo ASC");

// Busca autores para checkbox
$resultado_autores = mysqli_query($conn, "SELECT id, nome FROM autores ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Associar Autores a Livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/styles.css" />
</head>

<body>
    <header class="container-fluid">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4">Website de Livros</h1>
                <nav class="col text-end">
                    <a href="index.php">Página inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container-lg my-4">
        <h2>Associar Autores a Livro</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post" class="mb-5">
            <div class="mb-3">
                <label for="id_livro" class="form-label">Livro</label>
                <select id="id_livro" name="id_livro" class="form-select" required>
                    <option value="">Selecione um livro</option>
                    <?php while ($livro = mysqli_fetch_assoc($resultado_livros)): ?>
                        <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <fieldset class="mb-3">
                <legend>Autores</legend>
                <?php while ($autor = mysqli_fetch_assoc($resultado_autores)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="autores[]" value="<?= $autor['id'] ?>" id="autor-<?= $autor['id'] ?>" />
                        <label class="form-check-label" for="autor-<?= $autor['id'] ?>">
                            <?= htmlspecialchars($autor['nome']) ?>
                        </label>
                    </div>
                <?php endwhile; ?>
            </fieldset>

            <div class="mb-3">
                <label for="papel" class="form-label">Papel</label>
                <input type="text" class="form-control" id="papel" name="papel" placeholder="Ex: Autor, Coautor, Editor" required />
            </div>

            <button type="submit" class="btn btn-primary">Associar Autores</button>
        </form>

        <div class="editar">
            <h2>Outras Opções</h2>
            <a href="associar_livros_autores.php" class="btn btn-secondary">Associar Livros a Autor</a>
        </div>
    </main>

    <footer class="container-fluid text-center mt-5">
        <div class="container-lg">
            <p>&copy; 2025 Website de Livros.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>