<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (mysqli_connect_errno()) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$msg = '';


$autores = [];
$res = mysqli_query($conn, "SELECT id, nome FROM autores ORDER BY nome");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) { $autores[] = $row; }
    mysqli_free_result($res);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo   = $_POST['titulo'];
    $anos     = (int)$_POST['anos'];
    $autor_id = (int)$_POST['autor_id'];

    $diretorio_capas = 'uploads/capas/';

    
    if (!is_dir($diretorio_capas)) {
        mkdir($diretorio_capas, 0755, true);
    }

    $imagem = $_FILES['capa'];
    $fileName = basename($imagem['name']);
    $capa_caminho = $diretorio_capas . $fileName;

    
    $check = getimagesize($imagem['tmp_name']);
    if ($check == false) {
        $msg = "O ficheiro enviado não é uma imagem valida";
    } else {
        if (move_uploaded_file($imagem['tmp_name'], $capa_caminho)) {
            
            $sql = 'INSERT INTO livros (titulo, anos, capa) VALUES (?, ?, ?)';
            $query = mysqli_prepare($conn, $sql);
            if ($query) {
                mysqli_stmt_bind_param($query, 'sis', $titulo, $anos, $capa_caminho);
                if (mysqli_stmt_execute($query)) {
                    
                    $livro_id = mysqli_insert_id($conn);
                    $sql2 = 'INSERT INTO autor_livro (autor_id, livro_id) VALUES (?, ?)';
                    $q2 = mysqli_prepare($conn, $sql2);
                    if ($q2) {
                        mysqli_stmt_bind_param($q2, 'ii', $autor_id, $livro_id);
                        if (mysqli_stmt_execute($q2)) {
                            $msg = 'Livro inserido com sucesso!';
                        } else {
                            $msg = 'Erro ao associar autor: ' . mysqli_error($conn);
                        }
                        mysqli_stmt_close($q2);
                    } else {
                        $msg = 'Erro na preparação da associação: ' . mysqli_error($conn);
                    }
                } else {
                    $msg = 'Erro ao inserir livro: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($query);
            } else {
                $msg = 'Erro na preparação da query: ' . mysqli_error($conn);
            }
        }
    }
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">

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
    <div class="container-lg inserir">
        <h2>Inserir Novo Livro</h2>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form action="inserir_livro.php" method="POST" enctype="multipart/form-data" class="mb-5 inserir">
            <input type="text" name="titulo" placeholder="Título" required class="form-control mb-3" />
            <input type="number" name="anos" placeholder="Ano" required min="0" max="2099" step="1" class="form-control mb-3" />

            <label for="autor_id" class="form-label">Autor:</label>
            <select name="autor_id" id="autor_id" class="form-control mb-3" required>
                <?php foreach ($autores as $a): ?>
                    <option value="<?= (int)$a['id'] ?>"><?= $a['nome'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="capa" class="form-label">Capa do livro (imagem):</label>
            <input type="file" name="capa" id="capa" accept="image/*" required class="form-control mb-3" />
            <button type="submit" class="btn btn-primary">Inserir Livro</button>
        </form>
        <div class="editar">
            <h2>Opções</h2>
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