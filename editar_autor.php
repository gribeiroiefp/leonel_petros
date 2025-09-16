<?php
// --- CONECTAR À BASE DE DADOS ---
$conexao = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conexao) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// --- VARIÁVEIS ---
$mensagem = "";
$autor = null;

// --- APAGAR AUTOR ---
if (isset($_GET['apagar'])) {
    $id = (int) $_GET['apagar'];
    mysqli_query($conexao, "DELETE FROM autores WHERE id = $id");
    header('Location: editar_autor.php');
    exit;
}

// --- BUSCAR AUTOR PARA EDIÇÃO ---
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $autor = mysqli_fetch_assoc($resultado);
    }
}

// --- PROCESSAR FORMULÁRIO DE EDIÇÃO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($autor['id'])) {
    $id = (int)$autor['id'];

    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $data_nascimento = mysqli_real_escape_string($conexao, $_POST['data_nascimento']);
    $nacionalidade = mysqli_real_escape_string($conexao, $_POST['nacionalidade']);

    $caminho_foto = $autor['foto'] ?? '';

    if (!empty($_FILES['foto']['name'])) {
        $pasta_destino = __DIR__ . "/uploads/fotos/";
        $pasta_destino_bd = "uploads/fotos/";
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0755, true);
        }

        $ficheiro = $_FILES['foto'];
        $nome_ficheiro = basename($ficheiro['name']);
        $caminho_completo = $pasta_destino . $nome_ficheiro;
        $caminho_bd = $pasta_destino_bd . $nome_ficheiro;

        if (getimagesize($ficheiro['tmp_name']) !== false) {
            if (move_uploaded_file($ficheiro['tmp_name'], $caminho_completo)) {
                $caminho_foto = $caminho_bd;
            } else {
                $mensagem = "Erro: não consegui mover o ficheiro para $caminho_completo";
            }
        } else {
            $mensagem = "Erro: o ficheiro enviado não é uma imagem válida.";
        }
    }

    $caminho_foto = mysqli_real_escape_string($conexao, $caminho_foto);

    if (!$mensagem) {
        $sql = "UPDATE autores 
                SET nome = '$nome', 
                    data_nascimento = '$data_nascimento', 
                    nacionalidade = '$nacionalidade', 
                    foto = '$caminho_foto' 
                WHERE id = $id";

        if (mysqli_query($conexao, $sql)) {
            $mensagem = "Autor atualizado com sucesso!";
            $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
            if ($resultado && mysqli_num_rows($resultado) > 0) {
                $autor = mysqli_fetch_assoc($resultado);
            }
        } else {
            $mensagem = "Erro ao atualizar autor: " . mysqli_error($conexao);
        }
    }
}

// --- BUSCAR TODOS OS AUTORES PARA LISTAGEM ---
$lista_autores = mysqli_query(
    $conexao,
    "SELECT id, nome, foto FROM autores ORDER BY nome"
);

mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Autor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/styles.css" />
</head>
<body>
<header class="container-fluid">
    <div class="container-lg">
        <div class="row align-items-center">
            <h1 class="col-4">Biblioteca</h1>
            <nav class="col text-end">
                <a href="index.php" class="me-3 text-white">Página Inicial</a>
                <a href="pesquisa.php" class="text-white">Pesquisa</a>
            </nav>
        </div>
    </div>
</header>

<div class="container-lg">
    <h2 class="mb-4">Editar Autor</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <?php if ($autor): ?>
        <div class="d-flex justify-content-end mb-3">
            <a href="editar_autor.php?apagar=<?php echo $autor['id']; ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Tem a certeza que quer apagar este autor?');">
               Apagar
            </a>
        </div>

        <form action="editar_autor.php?id=<?php echo htmlspecialchars($autor['id']) ?>" method="POST" enctype="multipart/form-data" class="mb-5">
            <input type="text" name="nome" placeholder="Nome" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nome']) ?>" />
            <input type="date" name="data_nascimento" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['data_nascimento']) ?>" />
            <input type="text" name="nacionalidade" placeholder="Nacionalidade" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nacionalidade']) ?>" />
            <label for="foto" class="form-label">Foto do autor (imagem):</label>
            <input type="file" name="foto" id="foto" accept="image/*" class="form-control mb-3" />

            <?php if (!empty($autor['foto'])): ?>
                <img src="<?php echo htmlspecialchars($autor['foto']) ?>" alt="Foto do autor" class="mb-3" style="max-width: 200px; display: block;" />
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Editar Autor</button>
        </form>
    <?php elseif (!isset($_GET['id'])): ?>
        <p>Selecione um autor abaixo para editar.</p>
    <?php else: ?>
        <p>Autor não encontrado.</p>
    <?php endif; ?>

    <hr class="my-5">

    <h2 class="mb-4">Lista de Autores</h2>
    <div class="row">
        <?php
        if ($lista_autores && mysqli_num_rows($lista_autores) > 0) {
            while ($autor = mysqli_fetch_assoc($lista_autores)) {
                $id = $autor['id'];
                $nome = htmlspecialchars($autor['nome']);
                $foto = htmlspecialchars($autor['foto']);
                $foto_tag = $foto ? "<img src=\"$foto\" alt=\"$nome\" class=\"img-thumbnail\" style=\"max-width: 100px;\">" : "<div class='bg-secondary text-white text-center' style='width:100px;height:100px;display:flex;align-items:center;justify-content:center;'>Sem foto</div>";

                echo <<<HTML
                <div class="col-md-4 mb-4">
                    <a href="editar_autor.php?id=$id" class="text-decoration-none text-dark">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                $foto_tag
                                <h5 class="card-title mb-0">$nome</h5>
                            </div>
                        </div>
                    </a>
                </div>
HTML;
            }
        } else {
            echo "<p>Não existem autores cadastrados.</p>";
        }
        ?>
    </div>

    <div class="mt-4">
        <a href="inserir_livro.php" class="btn btn-primary me-2">Inserir Livro</a>
        <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
    </div>
</div>

<footer class="container-fluid text-center mt-5 py-3 bg-light">
    <div class="container-lg">
        <p>&copy; 2025 Website de Livros</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>