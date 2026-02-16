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
                class="thumb h-20 cursor-pointer border border-zinc-700 rounded-uw hover:border-primary transition"
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

            <div class="flex flex-wrap gap-3">
                <?php foreach($variants as $i => $v): ?>
                    <button
                    onclick="selectVariant(<?= $v['id'] ?>, <?= $v['preco'] ?>, this)"
                    class="variantBtn px-5 py-2.5 rounded-full border border-zinc-700
                    hover:border-primary hover:bg-white/5 transition
                    <?= $i===0 ? 'border-primary bg-primary/20' : '' ?>">
                        <?= $v['nome_variacao'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PREÇO -->
        <div class="text-4xl font-bold text-primary mb-4 tracking-tight" id="price">
            R$ <?= number_format($firstVariant['preco'] ?? 0,2,',','.') ?>
        </div>

        <div class="text-sm text-zinc-400 mb-6">
            Parcelamento disponível em até 12x no cartão
            <br>
            <span class="text-zinc-500 text-xs">
                O valor exato será exibido no pagamento
            </span>
        </div>

        <input type="hidden" id="variant_id" value="<?= $firstVariant['id'] ?>">

        <button onclick="addCart()"
        class="w-full bg-primary hover:bg-blue-500 text-white py-4 rounded-full text-lg font-semibold transition
        shadow-[0_12px_35px_rgba(59,130,246,0.45)] hover:shadow-[0_18px_50px_rgba(59,130,246,0.6)]">
        Adicionar ao carrinho
        </button>

    </div>

</div>


<script>
function selectVariant(id, price, btn){

    // muda preço
    document.getElementById('price').innerText =
        "R$ " + price.toFixed(2).replace('.',',');

    // destaque visual
    document.querySelectorAll('.variantBtn')
        .forEach(b=>b.classList.remove('border-primary','bg-primary/20'));

    btn.classList.add('border-primary','bg-primary/20');

    // troca imagem principal
    const img = document.querySelector(`.thumb[data-variant='${id}']`);
    if(img) document.getElementById('mainImage').src = img.src;
}
</script>
                    
<script>
function selectVariant(id, price, btn){

    document.getElementById('variant_id').value = id;

    document.getElementById('price').innerText =
        "R$ " + price.toFixed(2).replace('.',',');

    document.querySelectorAll('.variantBtn')
        .forEach(b=>b.classList.remove('border-primary','bg-primary/20'));

    btn.classList.add('border-primary','bg-primary/20');

    const img = document.querySelector(`.thumb[data-variant='${id}']`);
    if(img) document.getElementById('mainImage').src = img.src;
}

async function addCart(){

    const id = document.getElementById('variant_id').value;

    const res = await fetch("/ultraware_gaming/routes.php?action=add_to_cart",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded",
            "X-Requested-With":"XMLHttpRequest"
        },
        body:"variant_id="+id
    });

    if(res.status===401){
        window.location="/ultraware_gaming/public/login.php";
        return;
    }

    const data = await res.json();

    if(data.status==="ok"){
        updateCartBadge(data.count);
        showToast("Produto adicionado ao carrinho");
    }
}

</script>

<?php require_once ROOT . "/views/layout/footer.php"; ?>
