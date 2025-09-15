<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados do autor
$sql_autor = "SELECT id, nome, foto FROM autores WHERE id = $id";
$resultado_autor = mysqli_query($conn, $sql_autor);
$autor = mysqli_fetch_assoc($resultado_autor);

if (!$autor) {
    die('Autor não encontrado.');
}

// Buscar livros do autor
$sql_livros = "
    SELECT livros.id, livros.titulo, livros.capa, livros.anos
    FROM livros
    JOIN autor_livro ON livros.id = autor_livro.livro_id
    WHERE autor_livro.autor_id = $id
";
$resultado_livros = mysqli_query($conn, $sql_livros);

$caminho_foto_autor = (isset($autor['foto']) && $autor['foto'] != '') ? "/leonel_petros/uploads/fotos/" . $autor['foto'] : "imagens/placeholder_autor.jpg";
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($autor['nome']); ?> - Autor</title>
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
            <h2><?php echo htmlspecialchars($autor['nome']); ?></h2>
            <img src="<?php echo htmlspecialchars($caminho_foto_autor); ?>" alt="Foto do autor" class="img-fluid mb-3" style="max-height: 300px;">
        </section>

        <section>
            <h3>Livros do autor</h3>
            <a href="associar_autores_livros.php?id_autor=<?= $autor['id'] ?>" class="btn btn-primary mb-3">Associar Livro</a>
            <div class="row">
                <?php
                if ($resultado_livros && mysqli_num_rows($resultado_livros) > 0) {
                    while ($livro = mysqli_fetch_assoc($resultado_livros)) {
                        $livro_id = $livro['id'];
                        $titulo = htmlspecialchars($livro['titulo']);
                        $ano = htmlspecialchars($livro['anos']);
                        $caminho_capa = (isset($livro['capa']) && $livro['capa'] != '') ? "/leonel_petros/uploads/capas/" . $livro['capa'] : "imagens/placeholder_livro.jpg";

                        echo <<<HTML
                        <div class="col-md-4 mb-3">
                            <a href="livro.php?id=$livro_id" class="text-decoration-none text-white">
                                <div class="card h-100">
                                    <img src="$caminho_capa" class="card-img-top" alt="$titulo">
                                    <div class="card-body bg-dark text-white">
                                        <h5 class="card-title">$titulo</h5>
                                        <p class="card-text">Ano: $ano</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        HTML;
                    }
                } else {
                    echo "<p>Este autor ainda não possui livros cadastrados.</p>";
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