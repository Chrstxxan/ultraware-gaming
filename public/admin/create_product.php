<?php
require_once "../../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) {
    requireLogin();
}

/* BUSCA CATEGORIAS */
$stmt = $pdo->query("SELECT id,nome FROM categories ORDER BY nome ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once ROOT . "/views/admin/layout.php";
?>

<div class="glass-card p-8 space-y-10">

    <div class="space-y-2">
        <h2 class="text-2xl font-semibold">Cadastrar Produto</h2>
        <p class="text-zinc-400 text-sm">
            Preencha as informações e adicione variações ao produto
        </p>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <?php require_once ROOT . "/views/components/toast.php"; ?>
    <?php endif; ?>

    <form method="POST"
          action="/ultraware_gaming/routes.php?action=store_product"
          enctype="multipart/form-data"
          class="space-y-8">

        <!-- DADOS DO PRODUTO -->
        <div class="space-y-6">

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Nome do produto</label>
                <input name="nome" required
                class="w-full h-12 px-5 rounded-full
                bg-black/30 border border-white/10
                focus:border-primary focus:ring-2 focus:ring-primary/20
                transition">
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Descrição</label>
                <textarea name="descricao" required
                class="w-full min-h-[120px] max-h-[300px] px-5 py-3
                rounded-2xl bg-black/30 border border-white/10
                focus:border-primary focus:ring-2 focus:ring-primary/20
                transition resize-y"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Categoria</label>
                <select name="category_id" required
                class="w-full h-12 px-5 rounded-full
                bg-black/30 border border-white/10
                focus:border-primary focus:ring-2 focus:ring-primary/20
                transition">

                    <option value="">Selecione a categoria</option>

                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

        </div>

        <!-- VARIAÇÕES -->
        <div class="space-y-6">
            <h3 class="text-lg font-semibold">Variações</h3>

            <div id="variants" class="space-y-6"></div>

            <button type="button"
            onclick="addVariant()"
            class="bg-white/5 backdrop-blur-xl border border-white/15
            hover:bg-primary/20 px-6 h-11 rounded-full transition">
                + Adicionar variação
            </button>
        </div>

        <!-- BOTÃO -->
        <div class="pt-4">
            <button
            class="bg-primary hover:bg-blue-500 text-white
            px-8 py-3 rounded-full font-semibold
            transition shadow-[0_12px_35px_rgba(59,130,246,0.45)]
            hover:shadow-[0_18px_50px_rgba(59,130,246,0.6)]">
                Salvar Produto
            </button>
        </div>

    </form>

</div>


<script>
let variantIndex = 0;

function addVariant(){

    const container = document.getElementById('variants');

    container.insertAdjacentHTML('beforeend', `
    <div class="bg-white/5 backdrop-blur-2xl border border-white/15
    rounded-[24px] p-6 space-y-5 shadow-[0_0_40px_rgba(0,0,0,0.4)]">

        <input name="variants[${variantIndex}][nome]"
        placeholder="Ex: Preto / M / 128GB"
        class="w-full h-12 px-5 rounded-full
        bg-black/30 border border-white/10
        focus:border-primary focus:ring-2 focus:ring-primary/20 transition">

        <input type="number" step="0.01"
        name="variants[${variantIndex}][preco]"
        placeholder="Preço"
        class="w-full h-12 px-5 rounded-full
        bg-black/30 border border-white/10
        focus:border-primary focus:ring-2 focus:ring-primary/20 transition">

        <input type="number"
        name="variants[${variantIndex}][estoque]"
        placeholder="Estoque"
        class="w-full h-12 px-5 rounded-full
        bg-black/30 border border-white/10
        focus:border-primary focus:ring-2 focus:ring-primary/20 transition">

        <div>
            <label class="text-sm text-zinc-400">Imagens da variação</label>

            <label class="block border border-dashed border-white/20
            rounded-[20px] p-8 text-center cursor-pointer
            hover:border-primary hover:bg-primary/10 transition">

                <div class="space-y-2">
                    <p class="text-zinc-300 text-sm font-medium">
                        Selecionar imagens
                    </p>
                    <p class="text-xs text-zinc-500">
                        PNG, JPG ou WEBP
                    </p>
                </div>

                <input id="input_${variantIndex}"
                type="file"
                name="variant_images_${variantIndex}[]"
                multiple
                accept="image/*"
                class="hidden"
                onchange="previewImages(event, ${variantIndex})">

            </label>

            <div id="preview_${variantIndex}"
            class="flex flex-wrap gap-3 mt-4"></div>
        </div>

    </div>
`);

    variantIndex++;
}

addVariant();

function previewImages(event, index){

    const container = document.getElementById('preview_'+index);
    container.innerHTML = "";

    const files = event.target.files;

    [...files].forEach((file, i) => {

        const reader = new FileReader();

        reader.onload = e => {

            const div = document.createElement('div');
            div.className = "relative";

            div.innerHTML = `
                <img src="${e.target.result}"
                class="h-24 w-24 object-cover rounded-uw border border-zinc-700">

                <button type="button"
                onclick="removeImage(${index}, ${i})"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                    ×
                </button>
            `;

            container.appendChild(div);
        };

        reader.readAsDataURL(file);
    });
}

function removeImage(index, i){
    const input = document.getElementById('input_'+index);
    const dt = new DataTransfer();

    [...input.files].forEach((file, idx)=>{
        if(idx !== i) dt.items.add(file);
    });

    input.files = dt.files;

    previewImages({target:input}, index);
}
</script>

