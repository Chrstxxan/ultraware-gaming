<?php
require_once "../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

requireLogin();

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT 
        ci.variant_id,
        ci.quantity,
        v.nome_variacao,
        v.preco,
        p.nome,
        (
            SELECT path 
            FROM product_images 
            WHERE variant_id = v.id 
            LIMIT 1
        ) as img
    FROM cart_items ci
    JOIN product_variants v ON v.id = ci.variant_id
    JOIN products p ON p.id = v.product_id
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach($items as &$item){
    $item['subtotal'] = $item['preco'] * $item['quantity'];
    $total += $item['subtotal'];
}

require_once ROOT."/views/layout/header.php";
?>

<h1 class="text-3xl font-semibold mb-10">Seu carrinho</h1>

<?php if(empty($items)): ?>

<p class="text-zinc-400">Seu carrinho está vazio.</p>

<?php else: ?>

<div class="space-y-6">

<?php foreach($items as $item): ?>

<div class="flex gap-6 items-center bg-zinc-900 border border-zinc-800 rounded-2xl p-5">

    <img src="/ultraware_gaming/public/uploads/<?= $item['img'] ?>"
    class="h-24 w-24 object-cover rounded-xl">

    <div class="flex-1">
        <h3 class="font-semibold"><?= $item['nome'] ?></h3>
        <p class="text-sm text-zinc-400"><?= $item['nome_variacao'] ?></p>
    </div>

    <div class="text-primary font-bold text-lg">
        R$ <?= number_format($item['preco'],2,',','.') ?>
    </div>

    <!-- QUANTIDADE -->
    <div class="flex items-center gap-3">

        <button onclick="changeQty(<?= $item['variant_id'] ?>,-1)"
        class="bg-zinc-800 w-8 h-8 rounded-full">-</button>

        <span>
            <?= $item['quantity'] ?>
        </span>

        <button onclick="changeQty(<?= $item['variant_id'] ?>,1)"
        class="bg-zinc-800 w-8 h-8 rounded-full">+</button>

    </div>

    <!-- SUBTOTAL -->
    <div class="font-semibold">
        R$ <?= number_format($item['subtotal'],2,',','.') ?>
    </div>

    <!-- REMOVER -->
    <button onclick="removeItem(<?= $item['variant_id'] ?>)"
    class="text-red-400 hover:text-red-300">
        ✕
    </button>

</div>

<?php endforeach; ?>

</div>

<div class="mt-10 text-right">

    <div class="text-2xl font-bold mb-6">
        Total: <span class="text-primary">
        R$ <?= number_format($total,2,',','.') ?>
        </span>
    </div>

    <button class="bg-primary hover:bg-blue-500 px-8 py-4 rounded-full text-lg font-semibold">
        Finalizar compra
    </button>

</div>

<?php endif; ?>

<script>
function changeQty(id,delta){

    fetch("/ultraware_gaming/routes.php?action=update_cart",{
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded"},
        body:"variant_id="+id+"&delta="+delta
    }).then(()=>location.reload());
}

function removeItem(id){

    fetch("/ultraware_gaming/routes.php?action=remove_from_cart",{
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded"},
        body:"variant_id="+id
    }).then(()=>location.reload());
}
</script>

<?php require_once ROOT."/views/layout/footer.php"; ?>
