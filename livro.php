<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados do livro
$sql_livro = "SELECT id, titulo, anos, capa FROM livros WHERE id = $id";
$resultado_livro = mysqli_query($conn, $sql_livro);
$livro = mysqli_fetch_assoc($resultado_livro);

if (!$livro) {
    die('Livro não encontrado.');
}

// Buscar autores do livro
$sql_autores = "
    SELECT autores.id, autores.nome, autores.foto
    FROM autores
    JOIN autor_livro ON autores.id = autor_livro.autor_id
    WHERE autor_livro.livro_id = $id
";
$resultado_autores = mysqli_query($conn, $sql_autores);

$caminho_capa = (isset($livro['capa']) && $livro['capa'] != '') ? "/leonel_petros/uploads/capas/" . $livro['capa'] : "imagens/placeholder_livro.jpg";
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($livro['titulo']); ?> - Livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <header class="container-fluid py-3 mb-4">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4 text-white">Biblioteca</h1>
                <nav class="col text-end">
                    <a href="index.php" class="me-3">Página Inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container-lg">
        <section class="mb-5">
            <h2><?php echo htmlspecialchars($livro['titulo']); ?></h2>
            <img src="<?php echo htmlspecialchars($caminho_capa); ?>" alt="Capa do livro" class="img-fluid mb-3" style="max-height: 300px;">
            <p><strong>Ano:</strong> <?php echo htmlspecialchars($livro['anos']); ?></p>
        </section>

        <section>
            <h3>Autores do livro</h3>
            <a href="associar_autores_livros.php?id_livro=<?= $livro['id'] ?>" class="btn btn-primary mb-3">Associar Autor</a>
            <div class="row">
                <?php
                if ($resultado_autores && mysqli_num_rows($resultado_autores) > 0) {
                    while ($autor = mysqli_fetch_assoc($resultado_autores)) {
                        $autor_id = $autor['id'];
                        $nome = htmlspecialchars($autor['nome']);
                        $foto_autor = (isset($autor['foto']) && $autor['foto'] != '') ? "/leonel_petros/uploads/fotos/" . $autor['foto'] : "imagens/placeholder_autor.jpg";

                        echo <<<HTML
                        <div class="col-md-4 mb-3">
                            <a href="autor.php?id=$autor_id" class="text-decoration-none text-white">
                                <div class="card h-100">
                                    <img src="$foto_autor" class="card-img-top" alt="$nome">
                                    <div class="card-body bg-dark text-white">
                                        <h5 class="card-title">$nome</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        HTML;
                    }
                } else {
                    echo "<p>Este livro ainda não possui autores cadastrados.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="container-fluid text-center mt-5 py-3">
        <div class="container-lg">
            <p>&copy; 2025 Website de Livros</p>
        </div>
    </footer>
</body>
</html>