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

<h2 class="text-2xl font-semibold mb-6">Cadastrar Produto</h2>

<?php if(isset($_GET['success'])): ?>
<?php require_once ROOT . "/views/components/toast.php"; ?>
<?php endif; ?>

<form method="POST" action="/ultraware_gaming/routes.php?action=store_product"
      enctype="multipart/form-data" class="space-y-6">

    <!-- DADOS DO PRODUTO -->
    <input name="nome" placeholder="Nome do produto"
    required
    class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2">

    <textarea name="descricao" placeholder="Descrição completa"
    required
    class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2"></textarea>

    <!-- SELECT DE CATEGORIA -->
    <select name="category_id" required
    class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2
    focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">

        <option value="">Selecione a categoria</option>

        <?php foreach($categories as $c): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['nome']) ?>
            </option>
        <?php endforeach; ?>

    </select>


    <!-- VARIAÇÕES -->
    <h3 class="text-lg font-semibold mt-6">Variações</h3>

    <div id="variants" class="space-y-6"></div>

    <button type="button"
    onclick="addVariant()"
    class="bg-zinc-800 hover:bg-zinc-700 px-5 py-2 rounded-uw transition">
        + Adicionar variação
    </button>

    <!-- BOTÃO -->
    <button class="bg-primary hover:bg-blue-500 text-white px-6 py-3 rounded-uw font-semibold transition">
        Salvar Produto
    </button>

</form>


<script>
let variantIndex = 0;

function addVariant(){

    const container = document.getElementById('variants');

    container.insertAdjacentHTML('beforeend', `
        <div class="border border-zinc-800 rounded-uw p-5 space-y-5 bg-zinc-900/40">

            <input name="variants[${variantIndex}][nome]"
            placeholder="Ex: Preto / M / 128GB"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2">

            <input type="number" step="0.01"
            name="variants[${variantIndex}][preco]"
            placeholder="Preço"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2">

            <input type="number"
            name="variants[${variantIndex}][estoque]"
            placeholder="Estoque"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-uw px-4 py-2">

            <div>
                <label class="text-sm text-zinc-400">Imagens da variação</label>

                <label class="block border border-dashed border-zinc-700 rounded-uw p-8 text-center cursor-pointer hover:border-primary hover:bg-white/5 transition">

                    <div class="space-y-2">
                        <p class="text-zinc-300 text-sm font-medium">Selecionar imagens da variação</p>
                        <p class="text-xs text-zinc-500">PNG, JPG ou WEBP</p>
                    </div>

                    <input id="input_${variantIndex}"
                    type="file"
                    name="variant_images_${variantIndex}[]"
                    multiple
                    accept="image/*"
                    class="hidden"
                    onchange="previewImages(event, ${variantIndex})">

                </label>

                <div id="preview_${variantIndex}" class="flex flex-wrap gap-3 mt-4"></div>
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

<?php require_once ROOT . "/views/layout/footer.php"; ?>
