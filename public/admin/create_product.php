<?php
require_once "../../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";

if (!isLogged() || !user()['admin']) {
    die("Acesso restrito");
}

require_once ROOT . "/views/layout/header.php";
?>

<h2 class="text-2xl font-semibold mb-6">Cadastrar Produto</h2>

<?php if(isset($_GET['success'])): ?>
<?php require_once ROOT . "/views/components/toast.php"; ?>
<?php endif; ?>


<form method="POST" action="/ultraware_gaming/routes.php?action=store_product"
      enctype="multipart/form-data" class="space-y-6">

    <!-- DADOS DO PRODUTO -->
    <input name="nome" placeholder="Nome do produto"
    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2">

    <textarea name="descricao" placeholder="Descrição completa"
    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2"></textarea>

    <input name="categoria" placeholder="Categoria"
    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2">


    <!-- VARIAÇÕES -->
    <h3 class="text-lg font-semibold mt-6">Variações</h3>

    <div id="variants" class="space-y-6"></div>

    <button type="button"
    onclick="addVariant()"
    class="bg-zinc-800 hover:bg-zinc-700 px-4 py-2 rounded-lg">
        + Adicionar variação
    </button>


    <!-- BOTÃO -->
    <button class="bg-primary hover:bg-blue-500 transition px-6 py-3 rounded-lg font-semibold">
        Salvar Produto
    </button>

</form>


<script>
let variantIndex = 0;

function addVariant(){

    const container = document.getElementById('variants');

    container.insertAdjacentHTML('beforeend', `
        <div class="border border-zinc-800 rounded-xl p-4 space-y-4">

            <input name="variants[${variantIndex}][nome]"
            placeholder="Ex: Preto / M / 128GB"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2">

            <input type="number" step="0.01"
            name="variants[${variantIndex}][preco]"
            placeholder="Preço"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2">

            <input type="number"
            name="variants[${variantIndex}][estoque]"
            placeholder="Estoque"
            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2">

            <div>
                <label class="text-sm text-zinc-400">Imagens da variação</label>

                <div class="border-2 border-dashed border-zinc-700 rounded-xl p-6 text-center cursor-pointer hover:border-primary transition"
                    onclick="document.getElementById('input_${variantIndex}').click()">

                    <p class="text-zinc-400 text-sm">
                        Clique para selecionar imagens
                    </p>

                    <input id="input_${variantIndex}"
                    type="file"
                    name="variant_images_${variantIndex}[]"
                    multiple
                    accept="image/*"
                    class="hidden"
                    onchange="previewImages(event, ${variantIndex})">
                </div>

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
                class="h-24 w-24 object-cover rounded-lg border border-zinc-700">

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
