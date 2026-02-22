<?php require_once ROOT . "/views/layout/header.php"; ?>
<?php require_once ROOT . "/app/helpers/text.php";?>

<?php if(!empty($mostWanted)): ?>

<h2 class="text-2xl font-semibold mb-6 flex items-center gap-3">

<svg viewBox="0 0 24 24" class="w-6 h-6 text-primary drop-shadow-[0_0_10px_rgba(59,130,246,.8)]"
fill="none" stroke="currentColor" stroke-width="1.8">
<path stroke-linecap="round" stroke-linejoin="round"
d="M13 2L6 14h5l-1 8 8-14h-5l1-6z"/>
</svg>

Mais desejados

</h2>

<div class="relative mb-12">
    <button onclick="scrollWanted(-1)"
class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 z-10
w-11 h-11 items-center justify-center
rounded-full backdrop-blur-xl
bg-primary/20 hover:bg-primary/40
border border-white/10 text-blue-200
transition">
‹
</button>

<div id="wanted-row"
class="flex gap-5 overflow-x-auto scroll-smooth snap-x snap-mandatory
no-scrollbar px-4 md:px-10 pb-2"
style="scroll-padding-left:50%; scroll-padding-right:50%;">

<?php foreach ($mostWanted as $p): ?>

<a href="/ultraware_gaming/public/product.php?id=<?= $p['id'] ?>"
class="group relative shrink-0 snap-center
w-[82%] sm:w-[60%] md:w-[320px] lg:w-[280px] xl:w-[260px]">

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 shadow-lg
    hover:border-primary transition h-full flex flex-col">

        <?php if($p['img']): ?>
            <img src="/ultraware_gaming/public/uploads/<?= $p['img'] ?>"
            class="h-48 w-full object-contain mb-4">
        <?php endif; ?>

        <h3 class="text-lg font-semibold"><?= $p['nome'] ?></h3>

        <div class="mt-auto space-y-3">

            <span class="text-primary text-xl font-bold block">
                R$ <?= number_format($p['preco'],2,',','.') ?>
            </span>

            <span class="block text-center bg-primary text-white py-2.5 rounded-full font-medium
            hover:bg-blue-500 transition">
                Ver produto
            </span>

        </div>

    </div>

</a>

<?php endforeach; ?>

</div>

<button onclick="scrollWanted(1)"
class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 z-10
w-11 h-11 items-center justify-center
rounded-full backdrop-blur-xl
bg-primary/20 hover:bg-primary/40
border border-white/10 text-blue-200
transition">
›
</button>

</div>

<?php endif; ?>

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

<script>
const row = document.getElementById("wanted-row");
let autoScroll;
let hovering = false;

function getCards(){
    return [...row.querySelectorAll('a')];
}

function getCenteredIndex(){
    const center = row.scrollLeft + row.clientWidth / 2;

    let closest = 0;
    let minDist = Infinity;

    getCards().forEach((card, i) => {
        const cardCenter = card.offsetLeft + card.offsetWidth / 2;
        const dist = Math.abs(center - cardCenter);

        if (dist < minDist) {
            minDist = dist;
            closest = i;
        }
    });

    return closest;
}

function goToCard(index){
    const cards = getCards();
    if (!cards[index]) return;

    cards[index].scrollIntoView({
        behavior: "smooth",
        inline: "center",
        block: "nearest"
    });
}

function scrollWanted(dir){
    const next = getCenteredIndex() + dir;
    goToCard(next);
}

function startAuto(){
    autoScroll = setInterval(() => {

        if (hovering) return;

        let next = getCenteredIndex() + 1;

        if (next >= getCards().length) {
            next = 0;
        }

        goToCard(next);

    }, 3500);
}

function stopAuto(){
    clearInterval(autoScroll);
}

row.addEventListener("mouseenter", () => hovering = true);
row.addEventListener("mouseleave", () => hovering = false);

row.addEventListener("touchstart", stopAuto, { passive: true });
row.addEventListener("touchend", startAuto, { passive: true });

startAuto();
</script>   

<?php require_once ROOT . "/views/layout/footer.php"; ?>
