<?php require_once ROOT . "/views/layout/header.php"; ?>
<?php require_once ROOT . "/app/helpers/text.php";?>

<h2 class="text-2xl font-semibold mb-8">Produtos</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">

<?php foreach ($products as $p): ?>

<div class="relative group">

    <?php
    require_once ROOT."/app/helpers/favorites.php";

    $isFav = isLogged()
        ? isFavorite($pdo,$_SESSION['user']['id'],$p['id'])
        : false;

    $count = favoritesCount($pdo,$p['id']);
    ?>

    <!-- FAVORITO -->
    <button
    onclick="event.preventDefault();event.stopPropagation();toggleFavorite(<?= $p['id'] ?>,this)"
    class="fav-btn <?= $isFav?'active':'' ?>">

    <svg viewBox="0 0 24 24" fill="currentColor" class="fav-icon">
    <path d="M12 2l3 7 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>
    </svg>

    <span class="fav-count"><?= $count ?></span>

    </button>

    <!-- LINK DO PRODUTO -->
    <a href="/ultraware_gaming/public/product.php?id=<?= $p['id'] ?>"
       class="block">

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg
        hover:border-primary transition h-full flex flex-col">

            <?php if($p['img']): ?>
                <img src="/ultraware_gaming/public/uploads/<?= $p['img'] ?>"
                    class="h-48 w-full object-contain mb-4 transition group-hover:scale-[1.02]">
            <?php endif; ?>

            <h3 class="text-lg font-semibold"><?= $p['nome'] ?></h3>

            <p class="text-zinc-400 text-sm mt-1 mb-3 line-clamp-3 min-h-[60px]">
                <?= resumo($p['descricao'], 110) ?>
            </p>

            <div class="flex justify-between items-center mt-auto">
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

</div>

<?php endforeach; ?>

</div>

<?php require_once ROOT . "/views/layout/footer.php"; ?>
