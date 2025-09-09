<?php
// --- LIGAR À BASE DE DADOS ---
$conexao = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conexao) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Variáveis
$mensagem = "";
$livro = null;

// --- BUSCAR LIVRO SE O ID FOR FORNECIDO NA URL ---
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM livros WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $livro = mysqli_fetch_assoc($resultado);
    }
}

// --- PROCESSAR FORMULÁRIO SE FOI SUBMETIDO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($livro['id'])) {
    $id = (int)$livro['id'];
    $titulo = $_POST['titulo'];
    $anos = $_POST['anos'];

    // Capa inicial
    $caminho_capa = $livro['capa'] ?? '';

    // --- VERIFICAR SE UMA NOVA CAPA FOI ENVIADA ---
    if (!empty($_FILES['capa']['name'])) {
        $pasta_destino = __DIR__ . "/uploads/capas/";
        $pasta_destino_bd = "/leonel_petros/uploads/capas/";

        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0755, true);
        }

        $ficheiro = $_FILES['capa'];
        $nome_ficheiro = basename($ficheiro['name']);
        $caminho_completo = $pasta_destino . $nome_ficheiro;
        $caminho_bd = $pasta_destino_bd . $nome_ficheiro;

        if (getimagesize($ficheiro['tmp_name']) !== false) {
            if (move_uploaded_file($ficheiro['tmp_name'], $caminho_completo)) {
                $caminho_capa = $caminho_bd;
            } else {
                $mensagem = "Erro: não consegui mover o ficheiro para $caminho_completo";
            }
        } else {
            $mensagem = "Erro: o ficheiro enviado não é uma imagem válida.";
        }
    }

    // --- ATUALIZAR LIVRO ---
    if (!$mensagem) {
        $sql = "UPDATE livros SET titulo = ?, anos = ?, capa = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $titulo, $anos, $caminho_capa, $id);
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "Livro atualizado com sucesso!";
                $resultado = mysqli_query($conexao, "SELECT * FROM livros WHERE id = $id");
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $livro = mysqli_fetch_assoc($resultado);
                }
            } else {
                $mensagem = "Erro ao atualizar livro: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "Erro na preparação do SQL: " . mysqli_error($conexao);
        }
    }
}

// Fechar ligação
mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Livro</title>
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
                <a href="inserir_livro.php">Inserir Livro</a>
            </nav>
        </div>
    </div>
</header>

<div class="container-lg mt-5">
    <h2>Editar Livro</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <?php if ($livro): ?>
        <form action="editar_livro.php?id=<?php echo htmlspecialchars($livro['id']) ?>" method="POST" enctype="multipart/form-data" class="mb-5">
            <input
                type="text" name="titulo" placeholder="Título" required class="form-control mb-3" value="<?php echo htmlspecialchars($livro['titulo']) ?>" />
            <input
                type="text" name="anos" placeholder="Ano de publicação" required class="form-control mb-3" value="<?php echo htmlspecialchars($livro['anos']) ?>" />
            
                <label for="capa" class="form-label">Capa do livro:</label>
            <input type="file" name="capa" id="capa" accept="image/*" class="form-control mb-3" />

            <?php if (!empty($livro['capa'])): ?>
                <img src="<?php echo htmlspecialchars($livro['capa']) ?>" alt="Capa do livro" class="img-thumbnail" style="max-height: 200px;" />
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
    <?php else: ?>
        <p class="text-danger">Livro não encontrado.</p>
    <?php endif; ?>

    <div class="editar">
        <h2>Opções</h2>
        <a href="inserir_autor.php" class="btn btn-secondary">Inserir Novo Autor</a>
        <a href="index.php" class="btn btn-secondary">Voltar à Página Inicial</a>
    </div>
</div>

<footer class="container-fluid text-center mt-5">
    <div class="container-lg">
        <p>&copy; 2025 Website de Livros.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>