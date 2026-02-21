<?php require_once ROOT . "/views/layout/header.php"; ?>
<?php require_once ROOT . "/app/helpers/text.php";?>

<h2 class="text-2xl font-semibold mb-8">Meus favoritos</h2>

<?php if(!$products): ?>
<div class="text-zinc-400">
Você ainda não favoritou nenhum produto.
</div>
<?php else: ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">

<?php foreach ($products as $p): ?>

<a href="/ultraware_gaming/public/product.php?id=<?= $p['id'] ?>"
class="block group relative">

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg
    hover:border-primary transition h-full flex flex-col">

        <?php
        require_once ROOT."/app/helpers/favorites.php";

        $isFav = true; // aqui sempre será favorito
        $count = favoritesCount($pdo,$p['id']);
        ?>

        <button
        onclick="event.preventDefault();event.stopPropagation();toggleFavorite(<?= $p['id'] ?>,this)"
        class="fav-btn active">

            <svg viewBox="0 0 24 24" class="fav-icon">
                <path d="M12 2l3 7 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>
            </svg>

            <span class="fav-count"><?= $count ?></span>
        </button>

        <?php if($p['img']): ?>
            <img src="/ultraware_gaming/public/uploads/<?= $p['img'] ?>"
            class="h-48 w-full object-contain mb-4">
        <?php endif; ?>

        <h3 class="text-lg font-semibold"><?= $p['nome'] ?></h3>

        <p class="text-zinc-400 text-sm mt-1 mb-3 line-clamp-3 min-h-[60px]">
            <?= resumo($p['descricao'], 110) ?>
        </p>

        <div class="flex justify-between items-center mt-auto">

            <span class="text-primary text-xl font-bold">
                R$ <?= number_format($p['preco'],2,',','.') ?>
            </span>

            <span class="bg-primary text-white px-5 py-2.5 rounded-full font-medium">
                Ver produto
            </span>

        </div>

    </div>

</a>

<?php endforeach; ?>

</div>
<?php endif; ?>

<?php require_once ROOT . "/views/layout/footer.php"; ?>