    <?php
    require_once "../app/config/path.php";
    require_once ROOT."/app/config/database.php";
    require_once ROOT."/app/helpers/session.php";
    require_once ROOT."/app/helpers/auth.php";
    require_once ROOT."/app/helpers/cart_db.php";

    requireLogin();

    $userId = $_SESSION['user']['id'];

    /* carregar itens do carrinho */
    $stmt = $pdo->prepare("
        SELECT 
            ci.variant_id,
            ci.quantity,
            v.nome_variacao,
            v.preco,
            p.nome,
            (
                SELECT path
                FROM product_images
                WHERE variant_id = v.id
                LIMIT 1
            ) AS img
        FROM cart_items ci
        JOIN product_variants v ON v.id = ci.variant_id
        JOIN products p ON p.id = v.product_id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$items){
        header("Location: cart.php");
        exit;
    }

    $total = 0;
    foreach($items as $i=>$item){
        $subtotal = $item['preco']*$item['quantity'];
        $items[$i]['subtotal']=$subtotal;
        $total+=$subtotal;
    }

    require_once ROOT."/views/layout/header.php";
    ?>

    <h1 class="text-3xl font-semibold mb-10">Checkout</h1>

    <div class="grid md:grid-cols-2 gap-16 items-start">

    <!-- ================= ENDEREÇO ================= -->
    <form id="addressForm" class="space-y-4">

        <h2 class="text-xl font-semibold mb-4">Endereço de entrega</h2>

        <input name="nome" required placeholder="Nome completo" class="input">
        <input name="telefone" required placeholder="Telefone" class="input">
        <input name="cep" required placeholder="CEP" class="input">
        <input name="rua" required placeholder="Rua" class="input">
        <input name="numero" required placeholder="Número" class="input">

        <input name="complemento" placeholder="Complemento (opcional)" class="input">

        <input name="bairro" required placeholder="Bairro" class="input">
        <input name="cidade" required placeholder="Cidade" class="input">
        <select name="estado" required class="input appearance-none">
        <option value="">Selecione o estado</option>
        <option>AC</option><option>AL</option><option>AP</option><option>AM</option>
        <option>BA</option><option>CE</option><option>DF</option><option>ES</option>
        <option>GO</option><option>MA</option><option>MT</option><option>MS</option>
        <option>MG</option><option>PA</option><option>PB</option><option>PR</option>
        <option>PE</option><option>PI</option><option>RJ</option><option>RN</option>
        <option>RS</option><option>RO</option><option>RR</option><option>SC</option>
        <option>SP</option><option>SE</option><option>TO</option>
        </select>

        <button type="button" onclick="confirmarEndereco(event)"
        class="w-full bg-primary py-4 rounded-full font-semibold">
        Confirmar endereço
        </button>

        </form>

    <!-- ================= RESUMO ================= -->
    <div class="bg-zinc-900/70 backdrop-blur-3xl border border-white/10 rounded-uw p-8 h-fit sticky top-28">

    <h2 class="text-xl font-semibold mb-6">Resumo</h2>

    <?php foreach($items as $item): ?>
    <div class="flex gap-4 items-center mb-5">

        <img src="/ultraware_gaming/public/uploads/<?= $item['img'] ?>"
        class="h-16 w-16 rounded-xl object-cover">

        <div class="flex-1">
            <div class="font-medium leading-tight">
                <?= $item['nome'] ?>
            </div>

            <div class="text-sm text-zinc-400">
                <?= $item['nome_variacao'] ?>
            </div>

            <div class="text-sm text-zinc-400">
                <?= $item['quantity'] ?> x R$ <?= number_format($item['preco'],2,',','.') ?>
            </div>
        </div>

        <div class="font-semibold">
            R$ <?= number_format($item['subtotal'],2,',','.') ?>
        </div>

    </div>
    <?php endforeach; ?>

    <!-- ENTREGA (APARECE DEPOIS) -->
    <div id="shippingBox" class="hidden border-t border-zinc-800 pt-4 mt-4">
        <div class="flex justify-between">
            <span>Entrega (Correios)</span>
            <span id="shippingPrice">—</span>
        </div>

        <div class="flex justify-between text-lg font-bold mt-3">
            <span>Total</span>
            <span class="text-primary" id="finalTotal">—</span>
        </div>

        <form method="POST" action="/ultraware_gaming/routes.php?action=create_order" id="orderForm">
            <input type="hidden" name="frete" id="freteInput">
            <input type="hidden" name="dados" id="dadosInput">

            <button class="mt-6 w-full bg-primary py-4 rounded-full font-semibold">
                Ir para pagamento
            </button>
        </form>
    </div>

    </div>
    </div>

    <style>
    .input{
    width:100%;
    padding:12px 16px;
    border-radius:999px;
    background:#18181b;
    border:1px solid rgba(255,255,255,.1);
    }
    </style>

    <script>
    const subtotal = <?= $total ?>;

    async function confirmarEndereco(event){

        const btn = event.target;
        const form = document.getElementById("addressForm");

        if(!form.reportValidity()) return;

        const data = Object.fromEntries(new FormData(form));

        btn.innerText = "Calculando...";
        btn.disabled = true;

        const res = await fetch("/ultraware_gaming/routes.php?action=calcular_frete",{
            method:"POST",
            headers:{ "Content-Type":"application/json" },
            body: JSON.stringify(data)
        });

        let json;

        try{
            json = await res.json();
        }catch{
            alert("Erro ao calcular frete");
            btn.disabled = false;
            btn.innerText = "Confirmar endereço";
            return;
        }

        if(json.erro){
            alert("Frete indisponível para este CEP");
            btn.disabled = false;
            btn.innerText = "Confirmar endereço";
            return;
        }

        /* MOSTRA RESUMO */
        document.getElementById("shippingBox").classList.remove("hidden");

        document.getElementById("shippingPrice").innerText =
            "R$ "+json.valor.toFixed(2).replace('.',',')+
            " • "+json.prazo+" dias";

        const totalFinal = subtotal + json.valor;

        document.getElementById("finalTotal").innerText =
            "R$ "+totalFinal.toFixed(2).replace('.',',');

        /* ENVIA DADOS PARA O BACKEND */
        document.getElementById("freteInput").value = json.valor;
        document.getElementById("dadosInput").value = JSON.stringify(data);

        /* remove botão de confirmar endereço */
        btn.remove();
    }
    </script>


    <?php require_once ROOT."/views/layout/footer.php"; ?>
