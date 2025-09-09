<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Buscar os 3 livros mais recentes
$sql_livros = "SELECT id, titulo, anos, capa FROM livros ORDER BY anos DESC LIMIT 3";
$resultado_livros = mysqli_query($conn, $sql_livros);

// Buscar os 3 autores com mais livros
$sql_autores = "
    SELECT autores.id, autores.nome, autores.foto, COUNT(autor_livro.livro_id) AS total_livros
    FROM autores
    JOIN autor_livro ON autores.id = autor_livro.autor_id
    GROUP BY autores.id
    ORDER BY total_livros DESC
    LIMIT 3
";
$resultado_autores = mysqli_query($conn, $sql_autores);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Website de livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
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
        <!-- Livros -->
        <section class="mb-5">
            <h2>Livros</h2>
            <div class="row">
                <?php
                if ($resultado_livros && mysqli_num_rows($resultado_livros) > 0) {
                    while ($livro = mysqli_fetch_assoc($resultado_livros)) {
                        $id = $livro['id'];
                        $titulo = htmlspecialchars($livro['titulo']);
                        $ano = htmlspecialchars($livro['anos']);

                        if (isset($livro['capa']) && $livro['capa'] !== '') {
                            $capa = "/leonel_petros/uploads/capas/" . $livro['capa'];
                        } else {
                            $capa = "imagens/placeholder_livro.jpg";
                        }

                        echo <<<HTML
                        <div class="col-md-4 mb-3">
                            <a href="livro.php?id=$id" class="text-decoration-none text-white">
                                <div class="card h-100">
                                    <img src="$capa" class="card-img-top" alt="$titulo">
                                    <div class="card-body bg-dark text-white">
                                        <h5 class="card-title">$titulo</h5>
                                        <p class="card-text">Ano: $ano</p>
                                        <!-- Botão para editar livro -->
                                        <a href="editar_livro.php?id=$id" class="btn btn-warning btn-sm mt-2">Editar Livro</a>
                                    </div>
                                </div>
                            </a>
                        </div>
                        HTML;
                    }
                }
                ?>
            </div>
        </section>

        <!-- Autores -->
        <section>
            <h2>Autores</h2>
            <div class="row">
                <?php
                if ($resultado_autores && mysqli_num_rows($resultado_autores) > 0) {
                    while ($autor = mysqli_fetch_assoc($resultado_autores)) {
                        $id = $autor['id'];
                        $nome = htmlspecialchars($autor['nome']);

                        $total = $autor['total_livros'];

                        // Verifica se a foto existe e está definida corretamente
                        if (isset($autor['foto']) && $autor['foto'] !== '') {
                            $foto = "/leonel_petros/uploads/fotos/" . $autor['foto'];
                        } else {
                            $foto = "imagens/placeholder_autor.jpg";
                        }

                        echo <<<HTML
                        <div class="col-md-4 mb-3">
                            <a href="autor.php?id=$id" class="text-decoration-none text-white">
                                <div class="card h-100">
                                    <img src="$foto" class="card-img-top" alt="$nome">
                                    <div class="card-body bg-dark text-white">
                                        <h5 class="card-title">$nome</h5>
                                        <p class="card-text">Livros publicados: $total</p>
                                        <!-- Botão para editar autor -->
                                        <a href="editar_autor.php?id=$id" class="btn btn-warning btn-sm mt-2">Editar Autor</a>
                                    </div>
                                </div>
                            </a>
                        </div>
                        HTML;
                    }
                }
                ?>
            </div>
        </section>

        <div class="editar mt-5">
            <h2>Opções</h2>
            <a href="inserir_livro.php" class="btn btn-primary me-2">Inserir Livro</a>
            <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
        </div>
    </main>

    <footer class="container-fluid text-center mt-5 py-3">
        <div class="container-lg">
            <p>&copy; 2025 Website de Livros.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
</html>