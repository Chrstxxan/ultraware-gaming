<?php
require_once "../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";
require_once ROOT."/app/helpers/cart_db.php";

requireLogin();

$userId = $_SESSION['user']['id'];

/* carregar itens do carrinho */
$stmt = $pdo->prepare("
    SELECT 
        ci.variant_id,
        ci.quantity,
        v.nome_variacao,
        v.preco,
        p.nome
    FROM cart_items ci
    JOIN product_variants v ON v.id = ci.variant_id
    JOIN products p ON p.id = v.product_id
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$items){
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach($items as $i=>$item){
    $subtotal = $item['preco']*$item['quantity'];
    $items[$i]['subtotal']=$subtotal;
    $total+=$subtotal;
}

require_once ROOT."/views/layout/header.php";
?>

<h1 class="text-3xl font-semibold mb-10">Checkout</h1>

<div class="grid md:grid-cols-2 gap-10">

<!-- ENDEREÇO -->
<form method="POST" action="/ultraware_gaming/routes.php?action=create_order"
class="space-y-4">

<h2 class="text-xl font-semibold mb-4">Endereço de entrega</h2>

<input name="nome" placeholder="Nome completo"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="telefone" placeholder="Telefone"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="cep" placeholder="CEP"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="rua" placeholder="Rua"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="numero" placeholder="Número"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="bairro" placeholder="Bairro"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="cidade" placeholder="Cidade"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<input name="estado" placeholder="Estado"
class="w-full px-4 py-3 rounded-uw bg-zinc-900 border border-white/10">

<button class="w-full bg-primary py-4 rounded-full font-semibold">
Continuar para pagamento
</button>

</form>

<!-- RESUMO -->
<div class="bg-zinc-900 rounded-uw p-6 space-y-4">

<h2 class="text-xl font-semibold">Resumo</h2>

<?php foreach($items as $item): ?>
<div class="flex justify-between">
    <span><?= $item['nome'] ?> (<?= $item['nome_variacao'] ?>)</span>
    <span>R$ <?= number_format($item['subtotal'],2,',','.') ?></span>
</div>
<?php endforeach; ?>

<hr class="border-white/10">

<div class="flex justify-between text-xl font-bold">
    <span>Total</span>
    <span class="text-primary">
        R$ <?= number_format($total,2,',','.') ?>
    </span>
</div>

</div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>
