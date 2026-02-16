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
        pi.path AS img
    FROM cart_items ci
    JOIN product_variants v ON v.id = ci.variant_id
    JOIN products p ON p.id = v.product_id
    LEFT JOIN product_images pi 
        ON pi.variant_id = v.id
        AND pi.id = (
            SELECT id 
            FROM product_images 
            WHERE variant_id = v.id 
            ORDER BY id ASC
            LIMIT 1
        )
    WHERE ci.user_id = ?
");

$stmt->execute([$userId]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach($items as $key => $item){
    $subtotal = $item['preco'] * $item['quantity'];
    $items[$key]['subtotal'] = $subtotal;
    $total += $subtotal;
}

require_once ROOT."/views/layout/header.php";
?>

<h1 class="text-3xl font-semibold mb-10">Seu carrinho</h1>

<?php if(empty($items)): ?>

<p class="text-zinc-400">Seu carrinho está vazio.</p>

<?php else: ?>

<div class="space-y-6">

<?php foreach($items as $item): ?>

<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-4">

    <!-- linha superior -->
    <div class="flex gap-4">
        <img src="/ultraware_gaming/public/uploads/<?= $item['img'] ?>"
        class="h-24 w-24 object-contain rounded-xl flex-shrink-0">

        <div class="flex-1 min-w-0">
            <h3 class="font-semibold break-words leading-tight">
                <?= $item['nome'] ?>
            </h3>

            <p class="text-sm text-zinc-400 break-words">
                <?= $item['nome_variacao'] ?>
            </p>

            <div class="text-primary font-bold text-lg mt-2">
                R$ <?= number_format($item['preco'],2,',','.') ?>
            </div>
        </div>
    </div>


    <!-- linha inferior -->
    <div class="flex items-center justify-between gap-4">

        <!-- quantidade -->
        <div class="flex items-center gap-3 bg-zinc-800/70 px-3 py-2 rounded-full">
            <button onclick="changeQty(<?= $item['variant_id'] ?>,-1)"
            class="w-8 h-8 rounded-full hover:bg-white/10">-</button>

            <span class="min-w-[20px] text-center">
                <?= $item['quantity'] ?>
            </span>

            <button onclick="changeQty(<?= $item['variant_id'] ?>,1)"
            class="w-8 h-8 rounded-full hover:bg-white/10">+</button>
        </div>

        <!-- subtotal -->
        <div class="font-semibold">
            R$ <?= number_format($item['subtotal'],2,',','.') ?>
        </div>

        <!-- remover -->
        <button onclick="removeItem(<?= $item['variant_id'] ?>)"
        class="text-red-400 hover:text-red-300 text-lg px-2">
            ✕
        </button>

    </div>

</div>

<?php endforeach; ?>

</div>

<div class="mt-10 text-center sm:text-right">

    <div class="text-2xl font-bold mb-6">
        Total: <span class="text-primary">
        R$ <?= number_format($total,2,',','.') ?>
        </span>
    </div>

    <a href="/ultraware_gaming/public/checkout.php"
    class="inline-block text-center bg-primary hover:bg-blue-500 px-8 py-4 rounded-full text-lg font-semibold w-full sm:w-auto">
        Finalizar compra
    </a>

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
