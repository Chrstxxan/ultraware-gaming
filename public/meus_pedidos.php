<?php

require_once "../app/config/path.php";
require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

requireLogin();

$userId = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
SELECT id,total,payment_status,created_at
FROM orders
WHERE user_id=?
ORDER BY id DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT."/views/layout/header.php";
?>

<h1 class="text-3xl font-semibold mb-12">Meus pedidos</h1>

<div class="space-y-6">

<?php if(!$orders): ?>

<div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-10 text-center">
    <p class="text-zinc-400 mb-4">Você ainda não realizou nenhum pedido.</p>
    <a href="/ultraware_gaming/public/"
    class="inline-block bg-primary px-6 py-3 rounded-full font-medium hover:bg-blue-500 transition">
        Explorar produtos
    </a>
</div>

<?php else: foreach($orders as $o): ?>

<a href="/ultraware_gaming/public/pedido.php?id=<?= $o['id'] ?>"
class="block bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-8
hover:border-primary hover:shadow-[0_0_0_1px_rgba(59,130,246,0.4)] transition">

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

<div>
    <div class="text-lg font-semibold mb-1">
        Pedido #<?= $o['id'] ?>
    </div>

    <div class="text-sm text-zinc-400">
        <?= date('d/m/Y \à\s H:i', strtotime($o['created_at'])) ?>
    </div>
</div>

<div class="text-left md:text-right">

    <div class="text-xl font-semibold mb-1">
        R$ <?= number_format($o['total'],2,',','.') ?>
    </div>

    <div class="text-sm font-medium
    <?php
    switch($o['payment_status']){
        case 'paid': echo 'text-green-400'; break;
        case 'pending': echo 'text-yellow-400'; break;
        case 'failed': echo 'text-red-400'; break;
        default: echo 'text-zinc-400';
    }
    ?>">
        <?php
        switch($o['payment_status']){
            case 'paid': echo 'Pagamento aprovado'; break;
            case 'pending': echo 'Aguardando pagamento'; break;
            case 'failed': echo 'Pagamento recusado'; break;
            default: echo $o['payment_status'];
        }
        ?>
    </div>

</div>

</div>
</a>

<?php endforeach; endif; ?>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>
