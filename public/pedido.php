<?php

require_once "../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";
require_once ROOT."/app/helpers/order_status.php";

requireLogin();

$orderId = $_GET['id'] ?? null;
$userId  = $_SESSION['user']['id'];
$isAdmin = $_SESSION['user']['admin'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order) die("Pedido não encontrado");
if(!$isAdmin && $order['user_id'] != $userId) die("Acesso negado");

$publicStatus = getPublicOrderStatus($pdo, $orderId);

$stmt=$pdo->prepare("
SELECT oi.qtd,oi.preco_unitario,v.nome_variacao,p.nome
FROM order_items oi
JOIN product_variants v ON v.id=oi.variant_id
JOIN products p ON p.id=v.product_id
WHERE oi.order_id=?
");
$stmt->execute([$orderId]);
$items=$stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt=$pdo->prepare("SELECT * FROM order_addresses WHERE order_id=?");
$stmt->execute([$orderId]);
$addr=$stmt->fetch(PDO::FETCH_ASSOC);

$stmt=$pdo->prepare("SELECT * FROM order_tracking WHERE order_id=?");
$stmt->execute([$orderId]);
$tracking=$stmt->fetch(PDO::FETCH_ASSOC);

require_once ROOT."/views/layout/header.php";
?>

<a href="/ultraware_gaming/public/meus_pedidos.php"
class="text-zinc-400 hover:text-white mb-8 inline-block">
← Voltar para pedidos
</a>

<h1 class="text-3xl font-semibold mb-12">Pedido #<?= $order['id'] ?></h1>

<div class="grid lg:grid-cols-2 gap-14 items-start">

<!-- ITENS -->
<div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-10">

<h2 class="text-xl font-semibold mb-8">Itens do pedido</h2>

<?php foreach($items as $i): ?>
<div class="mb-6">

    <!-- linha principal -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-1">

        <div class="flex-1 min-w-0">

            <!-- nome -->
            <div class="font-medium break-words leading-snug text-[15px] md:text-base">
                <?= htmlspecialchars($i['nome']) ?>
            </div>

            <!-- preço MOBILE -->
            <div class="md:hidden font-semibold text-primary mt-2">
                R$ <?= number_format($i['qtd']*$i['preco_unitario'],2,',','.') ?>
            </div>

            <!-- variação -->
            <div class="text-sm text-zinc-400 mt-3">
                <?= htmlspecialchars($i['nome_variacao']) ?>
            </div>

            <!-- unitário -->
            <div class="text-sm text-zinc-400 mt-1">
                <?= $i['qtd'] ?> x R$ <?= number_format($i['preco_unitario'],2,',','.') ?>
            </div>

        </div>

        <!-- preço DESKTOP -->
        <div class="hidden md:block font-semibold whitespace-nowrap text-right">
            R$ <?= number_format($i['qtd']*$i['preco_unitario'],2,',','.') ?>
        </div>

    </div>

</div>
<?php endforeach; ?>

<hr class="border-zinc-800 my-8">

<div class="flex justify-between text-lg font-bold">
<span>Total</span>
<span class="text-primary">
R$ <?= number_format($order['total'],2,',','.') ?>
</span>
</div>

</div>

<!-- STATUS + ENDEREÇO -->
<div class="space-y-10">

<div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-8">

<h2 class="text-xl font-semibold mb-4">Status do pedido</h2>

<div class="text-lg font-semibold
<?php
switch($publicStatus){
    case 'novo': echo 'text-yellow-400'; break;
    case 'pago': echo 'text-blue-400'; break;
    case 'preparando': echo 'text-indigo-400'; break;
    case 'enviado': echo 'text-purple-400'; break;
    case 'entregue': echo 'text-green-400'; break;
    case 'cancelado': echo 'text-red-400'; break;
    default: echo 'text-zinc-400';
}
?>
">

<?php
switch($publicStatus){
    case 'novo': echo 'Aguardando pagamento'; break;
    case 'pago': echo 'Pagamento aprovado'; break;
    case 'preparando': echo 'Em preparação'; break;
    case 'enviado': echo 'Enviado'; break;
    case 'entregue': echo 'Entregue'; break;
    case 'cancelado': echo 'Cancelado'; break;
    default: echo $publicStatus;
}
?>

</div>

</div>
<?php if(($publicStatus === 'enviado' || $publicStatus === 'entregue') && $tracking): ?>

<div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-8 mt-8">

<h2 class="text-xl font-semibold mb-4">Rastreamento</h2>

<p class="text-zinc-300 mb-2">
Transportadora: <?= htmlspecialchars($tracking['carrier']) ?>
</p>

<p class="text-primary font-semibold text-lg">
<?= htmlspecialchars($tracking['tracking_code']) ?>
</p>

</div>

<?php endif; ?>

<div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-8">

<h2 class="text-xl font-semibold mb-4">Endereço de entrega</h2>

<p class="text-zinc-300 leading-relaxed">
<?= htmlspecialchars($addr['nome']) ?><br>
<?= htmlspecialchars($addr['rua']) ?>, <?= htmlspecialchars($addr['numero']) ?><br>
<?= htmlspecialchars($addr['bairro']) ?><br>
<?= htmlspecialchars($addr['cidade']) ?> - <?= htmlspecialchars($addr['estado']) ?><br>
CEP <?= htmlspecialchars($addr['cep']) ?>
</p>

</div>

</div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>
