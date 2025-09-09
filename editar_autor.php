<?php
// --- LIGAR À BASE DE DADOS ---
$conexao = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conexao) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Variáveis
$mensagem = "";
$autor = null;

// --- BUSCAR AUTOR SE O ID FOR FORNECIDO NA URL ---
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $autor = mysqli_fetch_assoc($resultado);
    }
}

// --- PROCESSAR FORMULÁRIO SE FOI SUBMETIDO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($autor['id'])) {
    $id = (int)$autor['id'];

    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $nacionalidade = $_POST['nacionalidade'];

    $nome = mysqli_real_escape_string($conexao, $nome);
    $data_nascimento = mysqli_real_escape_string($conexao, $data_nascimento);
    $nacionalidade = mysqli_real_escape_string($conexao, $nacionalidade);

    // Foto inicial
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

// Fechar ligação à base de dados
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
    <header class="container-fluid py-3 mb-4">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4">Biblioteca</h1>
                <nav class="col text-end">
                    <a href="index.php" class="me-3">Página Inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container-lg inserir">
        <h2>Editar Autor</h2>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if ($autor): ?>
            <form action="editar_autor.php?id=<?php echo htmlspecialchars($autor['id']) ?>" method="POST" enctype="multipart/form-data" class="mb-5 inserir">
                <input
                    type="text" name="nome" placeholder="Nome" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nome']) ?>" />
                <input
                    type="date" name="data_nascimento" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['data_nascimento']) ?>" />
                <input
                    type="text" name="nacionalidade" placeholder="Nacionalidade" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nacionalidade']) ?>" />
                    <label for="foto" class="form-label">Foto do autor (imagem):</label>
                <input
                    type="file" name="foto" id="foto" accept="image/*" class="form-control mb-3" />

                <?php if (!empty($autor['foto'])): ?>
                    <img
                        src="<?php echo htmlspecialchars($autor['foto']) ?>"
                        alt="Foto do autor"
                        class="foto mb-3"
                        style="max-width: 200px; display: block;"
                    />
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Editar Autor</button>
            </form>
        <?php else: ?>
            <p>Autor não encontrado.</p>
        <?php endif; ?>

        <div class="editar">
            <h2>Opções</h2>
            <a href="inserir_livro.php" class="btn btn-primary me-2">Inserir Livro</a>
            <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
        </div>
    </div>

    <footer class="container-fluid text-center mt-5 py-3">
        <div class="container-lg">
            <p>&copy; 2025 Website de Livros</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>