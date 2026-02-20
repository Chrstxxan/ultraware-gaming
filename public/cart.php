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
<div class="max-w-5xl mx-auto space-y-10">
    <h1 class="text-3xl font-semibold mb-10">Seu carrinho</h1>

    <?php if(empty($items)): ?>

    <p class="text-zinc-400">Seu carrinho está vazio.</p>

    <?php else: ?>

    <div class="space-y-6">

    <?php foreach($items as $item): ?>

    <div class="glass-card p-5 space-y-5">

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
        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">

            <!-- quantidade -->
            <div class="flex justify-start">
                <div class="flex items-center gap-3 bg-white/5 backdrop-blur-xl border border-white/10 px-3 py-2 rounded-full">
                    <button onclick="changeQty(<?= $item['variant_id'] ?>,-1)"
                    class="w-8 h-8 rounded-full hover:bg-primary/20 transition">-</button>

                    <span class="min-w-[20px] text-center">
                        <?= $item['quantity'] ?>
                    </span>

                    <button onclick="changeQty(<?= $item['variant_id'] ?>,1)"
                    class="w-8 h-8 rounded-full hover:bg-primary/20 transition">+</button>
                </div>
            </div>

            <!-- subtotal CENTRAL REAL -->
            <div class="text-center font-semibold text-lg">
                R$ <?= number_format($item['subtotal'],2,',','.') ?>
            </div>

            <!-- remover -->
            <div class="flex justify-end">
                <button onclick="removeItem(<?= $item['variant_id'] ?>)"
                class="text-red-400 hover:text-red-300 text-xl px-2 transition">
                    ✕
                </button>
            </div>

        </div>

    </div>

    <?php endforeach; ?>

    </div>

    <div class="glass-card p-8 mt-10 text-center space-y-6">

    <div class="text-2xl font-bold">
            Total:
            <span class="text-primary">
                R$ <?= number_format($total,2,',','.') ?>
            </span>
        </div>

        <a href="/ultraware_gaming/public/checkout.php"
        class="inline-flex items-center justify-center
        bg-primary hover:bg-blue-500
        px-8 py-4 rounded-full text-lg font-semibold
        transition shadow-[0_15px_40px_rgba(59,130,246,0.45)]">
            Finalizar compra
        </a>

    </div>

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
