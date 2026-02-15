<?php
require_once "../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) die("Produto não encontrado");


// TODAS as variações
$stmt = $pdo->prepare("
    SELECT * FROM product_variants 
    WHERE product_id = ?
    ORDER BY preco ASC
");
$stmt->execute([$id]);
$variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$firstVariant = $variants[0] ?? null;


// TODAS as imagens do produto
$stmt = $pdo->prepare("
    SELECT pi.path, v.id as variant_id
    FROM product_images pi
    JOIN product_variants v ON v.id = pi.variant_id
    WHERE v.product_id = ?
");
$stmt->execute([$id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT . "/views/layout/header.php";
?>

<div class="grid md:grid-cols-2 gap-10">

    <!-- GALERIA -->
    <div>

        <img id="mainImage"
        src="/ultraware_gaming/public/uploads/<?= $images[0]['path'] ?? 'no-image.png' ?>"
        class="w-full rounded-2xl border border-zinc-800 mb-4">

        <div id="thumbs" class="flex gap-3 overflow-x-auto">
            <?php foreach($images as $img): ?>
                <img src="/ultraware_gaming/public/uploads/<?= $img['path'] ?>"
                data-variant="<?= $img['variant_id'] ?>"
                class="thumb h-20 cursor-pointer border border-zinc-700 rounded-lg"
                onclick="document.getElementById('mainImage').src=this.src">
            <?php endforeach; ?>
        </div>

    </div>


    <!-- INFO -->
    <div>

        <h1 class="text-3xl font-semibold mb-4"><?= $product['nome'] ?></h1>

        <p class="text-zinc-400 mb-6"><?= $product['descricao'] ?></p>

        <!-- VARIAÇÕES -->
        <div class="mb-6">
            <p class="mb-2 text-sm text-zinc-400">Escolha uma opção:</p>

            <div class="flex flex-wrap gap-2">
                <?php foreach($variants as $v): ?>
                    <button
                    onclick="selectVariant(<?= $v['id'] ?>, <?= $v['preco'] ?>, this)"
                    class="variantBtn border border-zinc-700 px-4 py-2 rounded-lg hover:border-primary">
                        <?= $v['nome_variacao'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PREÇO -->
        <div class="text-3xl font-bold text-primary mb-4" id="price">
            R$ <?= number_format($firstVariant['preco'] ?? 0,2,',','.') ?>
        </div>

        <div class="text-sm text-zinc-400 mb-6">
            Parcelamento disponível em até 12x no cartão
            <br>
            <span class="text-zinc-500 text-xs">
                O valor exato será exibido no pagamento
            </span>
        </div>

        <button class="w-full bg-primary hover:bg-blue-500 py-4 rounded-xl text-lg font-semibold">
            Adicionar ao carrinho
        </button>

    </div>

</div>


<script>
function selectVariant(id, price, btn){

    // muda preço
    document.getElementById('price').innerText =
        "R$ " + price.toFixed(2).replace('.',',');

    // destaque botão
    document.querySelectorAll('.variantBtn')
        .forEach(b=>b.classList.remove('border-primary'));

    btn.classList.add('border-primary');

    // troca imagem principal pela primeira da variação
    const img = document.querySelector(`.thumb[data-variant='${id}']`);
    if(img) document.getElementById('mainImage').src = img.src;
}
</script>

<?php require_once ROOT . "/views/layout/footer.php"; ?>
