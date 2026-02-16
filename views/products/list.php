<?php require_once ROOT . "/views/layout/header.php"; ?>
<?php require_once ROOT . "/app/helpers/text.php";?>

<h2 class="text-2xl font-semibold mb-8">Produtos</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<?php foreach ($products as $p): ?>

    <a href="/ultraware_gaming/public/product.php?id=<?= $p['id'] ?>"
       class="block group">

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg
        hover:border-primary transition">

            <?php if($p['img']): ?>
                <img src="/ultraware_gaming/public/uploads/<?= $p['img'] ?>"
                    class="h-48 w-full object-contain mb-4 transition group-hover:scale-[1.02]">
            <?php endif; ?>

            <h3 class="text-lg font-semibold"><?= $p['nome'] ?></h3>

            <p class="text-zinc-400 text-sm mt-1 mb-3">
                <?= resumo($p['descricao'], 110) ?>
            </p>

            <div class="flex justify-between items-center mt-4">

                <span class="text-primary text-xl font-bold">
                    R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                </span>

                <span
                class="bg-primary text-white px-5 py-2.5 rounded-full font-medium
                shadow-[0_6px_18px_rgba(59,130,246,0.35)]
                group-hover:bg-blue-500 transition">
                    Ver produto
                </span>

            </div>

        </div>

    </a>

<?php endforeach; ?>

</div>

<?php require_once ROOT . "/views/layout/footer.php"; ?>
