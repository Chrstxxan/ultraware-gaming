<?php
require_once "../../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) {
    requireLogin();
}

if(!isset($_GET['id'])){
    die("Pedido não informado");
}

$order_id = (int) $_GET['id'];

/* PEDIDO */
$stmt = $pdo->prepare("
SELECT o.*, u.nome, u.email
FROM orders o
JOIN users u ON u.id = o.user_id
WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    die("Pedido não encontrado");
}

/* ENDEREÇO */
$stmt = $pdo->prepare("SELECT * FROM order_addresses WHERE order_id = ?");
$stmt->execute([$order_id]);
$address = $stmt->fetch(PDO::FETCH_ASSOC);

/* ITENS */
$stmt = $pdo->prepare("
SELECT 
    oi.qtd, 
    oi.preco_unitario, 
    pv.nome_variacao as variacao, 
    p.nome as produto
FROM order_items oi
JOIN product_variants pv ON pv.id = oi.variant_id
JOIN products p ON p.id = pv.product_id
WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* FRETE */
$stmt = $pdo->prepare("SELECT * FROM order_shipping WHERE order_id = ?");
$stmt->execute([$order_id]);
$shipping = $stmt->fetch(PDO::FETCH_ASSOC);

/* HISTÓRICO */
$stmt = $pdo->prepare("
SELECT status, created_at
FROM order_status_history
WHERE order_id = ?
ORDER BY created_at DESC
");
$stmt->execute([$order_id]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
$currentStatus = $history[0]['status'] ?? 'novo';

require_once ROOT . "/views/admin/layout.php";
?>

<div class="max-w-5xl mx-auto space-y-8">

<style>
.liquid{
background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
backdrop-filter: blur(26px) saturate(180%);
-webkit-backdrop-filter: blur(26px) saturate(180%);
border: 1px solid rgba(255,255,255,0.12);
box-shadow:
inset 0 1px 0 rgba(255,255,255,0.10),
0 30px 80px rgba(0,0,0,.55);
border-radius: 24px;
}
</style>

<!-- TOPO -->
<div class="liquid p-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-semibold">Pedido #<?= $order['id'] ?></h2>
        <p class="text-zinc-400 text-sm">
            <?= htmlspecialchars($order['nome']) ?> • <?= $order['email'] ?>
        </p>
    </div>

    <div class="text-right">
        <p class="text-primary text-xl font-semibold">
            R$ <?= number_format($order['total'],2,',','.') ?>
        </p>
        <p class="text-xs text-zinc-500">
            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
        </p>
    </div>
</div>


<div class="grid lg:grid-cols-3 gap-6">

<!-- ENDEREÇO -->
<div class="liquid p-8 lg:col-span-2">
<h3 class="font-semibold mb-4 text-zinc-200">Endereço de entrega</h3>

<div class="space-y-1 text-zinc-300">
<p><?= $address['rua'] ?>, <?= $address['numero'] ?></p>
<p><?= $address['bairro'] ?> - <?= $address['cidade'] ?>/<?= $address['estado'] ?></p>
<p class="text-zinc-400 text-sm">CEP <?= $address['cep'] ?></p>
</div>
</div>

<!-- FRETE -->
<div class="liquid p-8">
<h3 class="font-semibold mb-4 text-zinc-200">Frete</h3>

<?php if($shipping): ?>
<p class="text-zinc-300"><?= $shipping['service_name'] ?></p>
<p class="text-zinc-500 text-sm"><?= $shipping['delivery_days'] ?> dias úteis</p>
<p class="text-primary text-lg font-semibold mt-3">
R$ <?= number_format($shipping['price'],2,',','.') ?>
</p>
<?php else: ?>
<p class="text-zinc-500">Não registrado</p>
<?php endif; ?>

</div>

</div>


<!-- ITENS -->
<div class="liquid p-8 space-y-4">
    <h3 class="font-semibold">Itens do pedido</h3>

    <?php foreach($items as $i): ?>
        <div class="flex justify-between border-b border-white/10 pb-3">
            <div>
                <p><?= htmlspecialchars($i['produto']) ?></p>
                <p class="text-xs text-zinc-400"><?= htmlspecialchars($i['variacao']) ?></p>
            </div>

            <div class="text-right">
                <p>x<?= $i['qtd'] ?></p>
                <p class="text-primary">R$ <?= number_format($i['preco_unitario'],2,',','.') ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<div class="grid lg:grid-cols-2 gap-6">

<!-- HISTÓRICO -->
<div class="liquid p-8">
    <h3 class="font-semibold mb-4">Histórico do pedido</h3>

    <div class="space-y-3">
    <?php foreach($history as $h): ?>
        <div class="flex justify-between text-sm border-b border-white/10 pb-2">
            <span class="capitalize"><?= $h['status'] ?></span>
            <span class="text-zinc-500"><?= date('d/m H:i', strtotime($h['created_at'])) ?></span>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<?php
$stmt=$pdo->prepare("SELECT * FROM order_tracking WHERE order_id=?");
$stmt->execute([$order['id']]);
$tracking=$stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php if($currentStatus === 'preparando' || $currentStatus === 'enviado'): ?>

<div class="liquid p-8 space-y-4">

<h3 class="font-semibold">Rastreamento</h3>

<form method="POST" action="save_tracking.php" class="space-y-4">

<input type="hidden" name="order_id" value="<?= $order['id'] ?>">

<div>
<label class="text-sm text-zinc-400">Transportadora</label>
<input name="carrier" value="<?= htmlspecialchars($tracking['carrier'] ?? '') ?>"
class="w-full mt-1 px-4 h-11 rounded-full bg-black/30 border border-white/10">
</div>

<div>
<label class="text-sm text-zinc-400">Código de rastreio</label>
<input name="tracking_code" required value="<?= htmlspecialchars($tracking['tracking_code'] ?? '') ?>"
class="w-full mt-1 px-4 h-11 rounded-full bg-black/30 border border-white/10">
</div>

<button class="px-6 h-11 rounded-full bg-primary hover:bg-blue-500">
Salvar rastreio
</button>

</form>

</div>

<?php endif; ?>

<!-- AÇÕES -->
<div class="liquid p-8 space-y-4">

<h3 class="font-semibold">Ações</h3>

<div class="flex flex-wrap gap-3">

<?php if($currentStatus === 'novo'): ?>
<button onclick="openConfirmPayment()" class="px-5 h-11 rounded-full bg-primary/20 hover:bg-primary/30 text-primary transition">
Confirmar pagamento
</button>
<?php endif; ?>

<?php
$canUpdate = $currentStatus !== 'novo' && $currentStatus !== 'cancelado' && $currentStatus !== 'entregue';
?>

<div class="relative group">

<button
<?= $canUpdate ? 'onclick="toggleStatusMenu()"' : '' ?>
class="px-5 h-11 rounded-full transition
<?= $canUpdate
? 'bg-white/10 hover:bg-white/20'
: 'bg-white/5 text-zinc-500 cursor-not-allowed' ?>">
Atualizar status
</button>

<?php if(!$canUpdate): ?>
<div class="absolute left-0 mt-2 text-xs text-zinc-500 opacity-0 group-hover:opacity-100 transition">
Disponível após confirmação do pagamento
</div>
<?php endif; ?>

<?php if($canUpdate): ?>
<div id="statusMenu" class="hidden absolute mt-2 bg-zinc-900 border border-white/10 rounded-xl overflow-hidden">

<?php
$flow = [
'novo'=>['pago'],
'pago'=>['preparando'],
'preparando'=> $tracking ? ['enviado'] : [],
'enviado'=>['entregue']
];

foreach($flow[$currentStatus] ?? [] as $next):
?>
<form method="POST" action="update_order_status.php">
<input type="hidden" name="order_id" value="<?= $order['id'] ?>">
<button name="status" value="<?= $next ?>" class="block w-full text-left px-4 py-3 hover:bg-white/10 capitalize">
<?= $next ?>
</button>
</form>
<?php endforeach; ?>

</div>
<?php endif; ?>

</div>
</div>
</div>

</div>


<!-- MODAL CONFIRMAR PAGAMENTO -->
<div id="confirmPaymentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
<div class="bg-zinc-900 border border-white/10 rounded-2xl p-6 w-[90%] max-w-md space-y-5">
<h3 class="text-lg font-semibold">Confirmar pagamento?</h3>
<p class="text-sm text-zinc-400">Use apenas quando o pagamento for confirmado manualmente.</p>

<div class="flex justify-end gap-3">
<button onclick="closeConfirmPayment()" class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20">Cancelar</button>

<form method="POST" action="update_order_status.php">
<input type="hidden" name="order_id" value="<?= $order['id'] ?>">
<input type="hidden" name="status" value="pago">
<button class="px-4 py-2 rounded-full bg-primary hover:bg-blue-500">Confirmar</button>
</form>

</div>
</div>
</div>


<script>
function openConfirmPayment(){
document.getElementById('confirmPaymentModal').classList.remove('hidden');
document.getElementById('confirmPaymentModal').classList.add('flex');
}
function closeConfirmPayment(){
document.getElementById('confirmPaymentModal').classList.add('hidden');
document.getElementById('confirmPaymentModal').classList.remove('flex');
}
function toggleStatusMenu(){
document.getElementById('statusMenu').classList.toggle('hidden');
}
</script>