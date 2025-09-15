<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_autor = (int) ($_POST['id_autor'] ?? 0);
    $livros = $_POST['livros'] ?? [];
    $papel = mysqli_real_escape_string($conn, $_POST['papel'] ?? '');

    if ($id_autor > 0 && !empty($livros) && $papel !== '') {
        foreach ($livros as $id_livro) {
            $id_livro = (int) $id_livro;
            $sql = "INSERT INTO livro_autor (id_livro, id_autor, papel) VALUES ($id_livro, $id_autor, '$papel')";
            mysqli_query($conn, $sql);
        }
        $mensagem = "Livros associados ao autor com sucesso!";
    } else {
        $mensagem = "Por favor, selecione um autor, pelo menos um livro e informe o papel.";
    }
}

// Busca autores para dropdown
$resultado_autores = mysqli_query($conn, "SELECT id, nome FROM autores ORDER BY nome ASC");

// Busca livros para checkbox
$resultado_livros = mysqli_query($conn, "SELECT id, titulo FROM livros ORDER BY titulo ASC");
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Associar Livros a Autor</title>
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
        <h2>Associar Livros a Autor</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post" class="mb-5">
            <div class="mb-3">
                <label for="id_autor" class="form-label">Autor</label>
                <select id="id_autor" name="id_autor" class="form-select" required>
                    <option value="">Selecione um autor</option>
                    <?php while ($autor = mysqli_fetch_assoc($resultado_autores)): ?>
                        <option value="<?= $autor['id'] ?>"><?= htmlspecialchars($autor['nome']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <fieldset class="mb-3">
                <legend>Livros</legend>
                <?php while ($livro = mysqli_fetch_assoc($resultado_livros)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="livros[]" value="<?= $livro['id'] ?>" id="livro-<?= $livro['id'] ?>" />
                        <label class="form-check-label" for="livro-<?= $livro['id'] ?>">
                            <?= htmlspecialchars($livro['titulo']) ?>
                        </label>
                    </div>
                <?php endwhile; ?>
            </fieldset>

            <div class="mb-3">
                <label for="papel" class="form-label">Papel</label>
                <input type="text" class="form-control" id="papel" name="papel" placeholder="Ex: Autor, Coautor, Editor" required />
            </div>

            <button type="submit" class="btn btn-primary">Associar Livros</button>
        </form>

        <div class="editar">
            <h2>Outras Opções</h2>
            <a href="associar_autores_a_livros.php" class="btn btn-secondary">Associar Autores a Livro</a>
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