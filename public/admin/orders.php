<?php
require_once "../../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT . "/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) {
    requireLogin();
}

/* BUSCA PEDIDOS */
$stmt = $pdo->query("
    SELECT 
        o.id,
        o.total,
        o.payment_status,
        o.created_at,
        u.nome AS cliente
    FROM orders o
    JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT . "/views/admin/layout.php";
?>

<div class="glass-card p-8 space-y-8">

    <div class="space-y-2">
        <h2 class="text-2xl font-semibold">Pedidos</h2>
        <p class="text-zinc-400 text-sm">
            Gerencie e acompanhe os pedidos da loja
        </p>
    </div>

    <div class="space-y-4">

        <?php foreach($orders as $o): ?>

            <a href="order.php?id=<?= $o['id'] ?>"
            class="block bg-white/5 backdrop-blur-xl border border-white/15
            hover:border-primary hover:bg-primary/10
            rounded-[20px] p-6 transition">

                <div class="flex justify-between items-center">

                    <div class="space-y-1">
                        <p class="font-semibold text-lg">
                            Pedido #<?= $o['id'] ?>
                        </p>

                        <p class="text-sm text-zinc-400">
                            <?= htmlspecialchars($o['cliente']) ?>
                        </p>

                        <p class="text-xs text-zinc-500">
                            <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                        </p>
                    </div>

                    <div class="text-right space-y-2">
                        <p class="text-primary font-semibold">
                            R$ <?= number_format($o['total'],2,',','.') ?>
                        </p>

                        <span class="text-xs px-3 py-1 rounded-full
                        bg-primary/20 text-primary">
                            <?= $o['payment_status'] ?>
                        </span>
                    </div>

                </div>

            </a>

        <?php endforeach; ?>

    </div>

</div>