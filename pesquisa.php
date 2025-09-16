<?php

$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$resultados = [];

if (isset($_GET['string']) && !empty($_GET['string'])) {
    $string = $_GET['string'];
    $sql = "
        SELECT livros.id,
               livros.titulo,
               livros.capa,
               livros.anos,
               GROUP_CONCAT(autores.nome SEPARATOR ', ') AS autor
        FROM livros
        JOIN autor_livro ON livros.id = autor_livro.livro_id
        JOIN autores ON autor_livro.autor_id = autores.id
        WHERE livros.titulo LIKE '%$string%' OR autores.nome LIKE '%$string%'
        GROUP BY livros.id
    ";
    $resultados = mysqli_query($conn, $sql);
}

?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
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
<div class="container-lg pesquisa">
    <div class="pesquisa-form">
        <h2>Pesquisa</h2>
        <form action="" method="GET">
            <div class="sombra-form">
                <?php if (isset($_GET['string'])): ?>
                    <input type="text" name="string" placeholder="Procurar por título ou autor" required value="<?= $_GET['string'] ?>">
                <?php else: ?>
                    <input type="text" name="string" placeholder="Procurar por título ou autor" required>
                <?php endif; ?>
                <button type="submit">Pesquisar</button>
            </div>
        </form>
    </div>
    <?php if (isset($_GET['string']) && !$_GET['string'] == ''): ?>
        <div class="lista col">
            <h3>Resultados</h3>
            <?php
            if ($resultados && mysqli_num_rows($resultados) > 0) {
                while ($livro = mysqli_fetch_assoc($resultados)) {
                    $id = $livro['id'];
                    $capa = $livro['capa'];
                    $titulo = htmlspecialchars($livro['titulo']);
                    $anos = htmlspecialchars($livro['anos']);
                    $autor = htmlspecialchars($livro['autor']);

                    echo <<<HTML
                    <a href="editar_livro.php?id=$id" class="filme_link row align-items-end">
                        <img src="$capa" alt="capa do livro" class="col-3">
                        <div class="filme col">
                            <h3>$titulo</h3>
                            <p>$anos</p>
                            <p>$autor</p>
                        </div>
                    </a>
                    HTML;
                }
            }
            ?>
        </div>
    <?php endif; ?>
    <div class="editar">
        <h2>Opções</h2>
        <a href="inserir_livro.php" class="btn btn-primary">Inserir Livro</a>
        <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
    </div>
</div>
<footer class="container-fluid text-center">
    <div class="container-lg">
        <p>&copy; 2025 Website de Livros.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
 
</html>