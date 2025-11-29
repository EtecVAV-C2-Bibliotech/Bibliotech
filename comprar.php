<?php
// comprar.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'banco.php'; // seu arquivo de conexão - deve definir $connect (mysqli)

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

// Mensagem vindo via GET (processar_compra.php usa redirect)
$msg_get = $_GET['msg'] ?? '';

// Busca livros (agora incluindo campo imagem se existir)
$sql = "SELECT idLivro, titulo, autor, quantidade, imagem FROM livros ORDER BY titulo";
$result = mysqli_query($connect, $sql);
if ($result === false) {
    die("Erro ao buscar livros: " . mysqli_error($connect));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Comprar Livros</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
/* modal styles */
.modal-bg {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.modal-box {
    background: white;
    width: 420px;
    max-width: 92%;
    padding: 20px;
    border-radius: 10px;
    animation: aparecer .16s ease;
}
@keyframes aparecer {
    from {transform: scale(0.95); opacity: 0;}
    to   {transform: scale(1); opacity: 1;}
}
.modal-close {
    float: right;
    cursor: pointer;
    font-size: 20px;
}

/* capa pequena */
.capa-livro {
    width: 56px;
    height: 78px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* pequenas melhorias visuais */
.container { padding-top: 20px; }
.table td, .table th { vertical-align: middle; }
</style>
</head>
<body class="p-4">

<div class="container">
    <h2>Comprar Livros</h2>
    <p>Usuário: <strong><?= htmlspecialchars($_SESSION['nickname'] ?? '---') ?></strong></p>

    <?php if (!empty($msg_get)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg_get) ?></div>
    <?php endif; ?>

    <table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Capa</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Disponível</th>
            <th>Comprar</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>
                    <?php if (!empty($row['imagem'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['imagem']) ?>" class="capa-livro" alt="Capa">
                    <?php else: ?>
                        <img src="img/sem_capa.png" class="capa-livro" alt="Sem capa">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['titulo']) ?></td>
                <td><?= htmlspecialchars($row['autor']) ?></td>
                <td><?= (int)$row['quantidade'] ?></td>
                <td>
                    <?php if ((int)$row['quantidade'] > 0): ?>
                        <div class="d-flex gap-2 align-items-center">
                            <!-- input de quantidade local (não submete) -->
                            <input id="qtd_<?= (int)$row['idLivro'] ?>" type="number" value="1" min="1" max="<?= (int)$row['quantidade'] ?>" class="form-control" style="width:90px">
                            <!-- botão abre modal (type=button evita submit do form pai) -->
                            <button type="button" class="btn btn-success"
                                onclick="abrirPagamento(<?= (int)$row['idLivro'] ?>, document.getElementById('qtd_<?= (int)$row['idLivro'] ?>').value)">
                                Comprar
                            </button>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">Sem estoque</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    </table>

    <a href="indexcli.php" class="btn btn-secondary">Voltar</a>
</div>

<!-- MODAL: aqui fica fora de qualquer form da tabela -->
<div class="modal-bg" id="pagamentoModal" aria-hidden="true">
    <div class="modal-box">
        <span class="modal-close" onclick="fecharPagamento()">✖</span>
        <h2>Pagamento</h2>

        <!-- Form que envia para processar_compra.php (mantém sua lógica atual) -->
        <form method="POST" action="processar_compra.php">
            <!-- Campos ocultos preenchidos pela função abrirPagamento -->
            <input type="hidden" name="idLivro" id="modal_idLivro" value="">
            <input type="hidden" name="qtd" id="modal_qtd" value="1">

            <div class="mb-2">
                <label><strong>Forma de Pagamento:</strong></label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pagamento" id="r_px" value="pix" onclick="mostrarTipo('pix')">
                    <label class="form-check-label" for="r_px">PIX</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pagamento" id="r_card" value="cartao" onclick="mostrarTipo('cartao')">
                    <label class="form-check-label" for="r_card">Cartão de Crédito</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_pagamento" id="r_cash" value="dinheiro" onclick="mostrarTipo('dinheiro')">
                    <label class="form-check-label" for="r_cash">Dinheiro</label>
                </div>
            </div>

            <div id="pag_pix" style="display:none; margin-bottom:10px;">
                <p>Escaneie o QR Code:</p>
                <img src="qrcode_exemplo.png" width="150" alt="QRCode">
            </div>

            <div id="pag_cartao" style="display:none; margin-bottom:10px;">
                <div class="mb-2">
                    <label>Número do Cartão:</label>
                    <input type="text" name="numero_cartao" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Nome no Cartão:</label>
                    <input type="text" name="nome_cartao" class="form-control">
                </div>
                <div class="mb-2">
                    <label>CVV:</label>
                    <input type="text" name="cvv" class="form-control" style="width:120px;">
                </div>
            </div>

            <div id="pag_dinheiro" style="display:none; margin-bottom:10px;">
                <label>Troco para quanto?</label>
                <input type="number" name="troco_para" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" onclick="fecharPagamento()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar Pagamento</button>
            </div>
        </form>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
function abrirPagamento(idLivro, qtd) {
    // preenche inputs hidden do modal
    document.getElementById('modal_idLivro').value = parseInt(idLivro, 10);
    // garante que qtd é um número e respeita mínimo 1
    let q = parseInt(qtd, 10);
    if (!q || q < 1) q = 1;
    document.getElementById('modal_qtd').value = q;

    // reset radios e seções
    const radios = document.querySelectorAll('input[name="tipo_pagamento"]');
    radios.forEach(r => r.checked = false);
    mostrarTipo(null);

    // mostra modal
    const modal = document.getElementById('pagamentoModal');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function fecharPagamento() {
    const modal = document.getElementById('pagamentoModal');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
}

function mostrarTipo(tipo) {
    document.getElementById("pag_pix").style.display = "none";
    document.getElementById("pag_cartao").style.display = "none";
    document.getElementById("pag_dinheiro").style.display = "none";
    if (tipo) {
        const el = document.getElementById("pag_" + tipo);
        if (el) el.style.display = "block";
    }
}
</script>

</body>
</html>

<?php mysqli_close($connect); ?>
