<?php

require_once __DIR__ . "/app/config/path.php";

require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/helpers/flash.php";

$action = $_GET['action'] ?? null;

switch($action){

/* ================= LOGIN ================= */

case "login":

    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])){

        $_SESSION['user'] = $user;

        /* =====================================
           EXECUTA AÇÃO QUE O USUÁRIO TENTOU FAZER
        ===================================== */
        if(isset($_SESSION['pending_action'])){

            $pending = $_SESSION['pending_action'];

            if(($pending['type'] ?? null) === 'add_to_cart'){
                $variantId = $pending['data']['variant_id'] ?? null;

                if($variantId){
                    addToCart($variantId, 1);
                    flash('success','Produto adicionado ao carrinho!');
                }
            }

            unset($_SESSION['pending_action']);
        }

        /* redireciona para onde estava */
        $redirect = $_SESSION['redirect_after_login'] ?? "/ultraware_gaming/public";
        unset($_SESSION['redirect_after_login']);

        header("Location: $redirect");
        exit;

    }else{
        flash('error',"Email ou senha inválidos");
        header("Location: /ultraware_gaming/public/login.php");
        exit;
    }

break;


/* ================= REGISTER ================= */

case "register":

    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if($senha !== $confirmar){
        flash('error',"As senhas não coincidem, verifique e tente novamente.");
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

    // verifica se email já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->fetch()){
        flash('error',"Este email já está cadastrado");
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

    $hash = password_hash($senha,PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (nome,email,senha,admin,criado_em)
        VALUES (?,?,?,0,NOW())
    ");
    $stmt->execute([$nome,$email,$hash]);

    // login automático após cadastro
    $_SESSION['user'] = [
        'id'=>$pdo->lastInsertId(),
        'nome'=>$nome,
        'email'=>$email,
        'admin'=>0
    ];

    $redirect = $_SESSION['redirect_after_login'] ?? "/ultraware_gaming/public";
    unset($_SESSION['redirect_after_login']);

    header("Location: $redirect");
    exit;
break;


/* ================= LOGOUT ================= */

case "logout":
    session_destroy();
    header("Location: /ultraware_gaming/public/index.php");
    exit;
break;


/* ================= CRIAR PRODUTO (ADMIN) ================= */

case "store_product":

    if (!isset($_SESSION['user']) || !$_SESSION['user']['admin']){
        die("Acesso negado");
    }

    /* valida categoria */
    if(empty($_POST['category_id'])){
        die("Categoria não informada");
    }

    /* cria produto */
    $stmt = $pdo->prepare("
        INSERT INTO products (nome, descricao, category_id)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['category_id']
    ]);

    $productId = $pdo->lastInsertId();

    /* cria variações */
    if(!empty($_POST['variants'])){
        foreach ($_POST['variants'] as $index => $variant){

            if(empty($variant['nome'])) continue;

            $stmt = $pdo->prepare("
                INSERT INTO product_variants (product_id, nome_variacao, preco, estoque)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $productId,
                $variant['nome'],
                $variant['preco'] ?? 0,
                $variant['estoque'] ?? 0
            ]);

            $variantId = $pdo->lastInsertId();

            /* upload imagens */
            $inputName = "variant_images_" . $index;

            if (!empty($_FILES[$inputName]['name'][0])) {
                foreach ($_FILES[$inputName]['tmp_name'] as $k => $tmp){

                    if(!$tmp) continue;

                    $ext = pathinfo($_FILES[$inputName]['name'][$k], PATHINFO_EXTENSION);
                    $name = uniqid().".".$ext;

                    move_uploaded_file($tmp, __DIR__."/public/uploads/".$name);

                    $stmt = $pdo->prepare("
                        INSERT INTO product_images (variant_id, path)
                        VALUES (?, ?)
                    ");

                    $stmt->execute([$variantId, $name]);
                }
            }
        }
    }

    header("Location: /ultraware_gaming/public/admin/create_product.php?success=1");
    exit;
break;


/* ================= CARRINHO ================= */

case "add_to_cart":

    require_once __DIR__."/app/helpers/auth.php";
    requireLogin();

    require_once __DIR__."/app/helpers/cart_db.php";

    $variant = $_POST['variant_id'] ?? null;

    if($variant){
        addToCartDB($pdo,$_SESSION['user']['id'],$variant);

        echo json_encode([
            "status"=>"ok",
            "count"=>cartCountDB($pdo,$_SESSION['user']['id'])
        ]);
    }
    exit;
break;



case "update_cart":

    require_once __DIR__."/app/helpers/auth.php";
    requireLogin();

    require_once __DIR__."/app/helpers/cart_db.php";

    updateCartDB(
        $pdo,
        $_SESSION['user']['id'],
        $_POST['variant_id'],
        $_POST['delta']
    );

    echo "ok";
    exit;
break;



case "remove_from_cart":

    require_once __DIR__."/app/helpers/auth.php";
    requireLogin();

    require_once __DIR__."/app/helpers/cart_db.php";

    removeFromCartDB(
        $pdo,
        $_SESSION['user']['id'],
        $_POST['variant_id']
    );

    echo "ok";
    exit;
break;



case "create_order":

require_once __DIR__."/app/helpers/auth.php";
require_once __DIR__."/app/helpers/shipping.php";
require_once __DIR__."/app/helpers/mercadopago.php";
requireLogin();

/* ================= PEGAR DADOS DO CHECKOUT ================= */

$dados = json_decode($_POST['dados'] ?? '{}', true);

if(!$dados){
    flash('error','Endereço inválido');
    header("Location: /ultraware_gaming/public/checkout.php");
    exit;
}

/* ================= SANITIZAÇÃO ================= */

$dados['telefone'] = preg_replace('/\D/','',$dados['telefone'] ?? '');
$dados['cep']      = preg_replace('/\D/','',$dados['cep'] ?? '');
$dados['numero']   = trim($dados['numero'] ?? '');
$dados['complemento'] = trim($dados['complemento'] ?? '') ?: null;

/* valida obrigatório */
$required=['nome','telefone','cep','rua','numero','bairro','cidade','estado'];

foreach($required as $f){
    if(empty($dados[$f])){
        flash('error','Preencha todos os campos obrigatórios');
        header("Location: /ultraware_gaming/public/checkout.php");
        exit;
    }
}

$pdo->beginTransaction();

try{

$userId=$_SESSION['user']['id'];

/* ================= PEGAR CARRINHO ================= */

$stmt=$pdo->prepare("
SELECT ci.variant_id,ci.quantity,v.preco
FROM cart_items ci
JOIN product_variants v ON v.id=ci.variant_id
WHERE ci.user_id=?
");
$stmt->execute([$userId]);
$cart=$stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$cart){
    throw new Exception("Carrinho vazio");
}

/* ================= CALCULAR SUBTOTAL ================= */

$subtotal=0;
foreach($cart as $item){
    $subtotal+=$item['preco']*$item['quantity'];
}

/* ================= FRETE REAL (ANTI-FRAUDE) ================= */

$shipping = calculateShipping($dados['cep']);

if(isset($shipping['erro'])){
    throw new Exception("Falha ao calcular frete");
}

$total = $subtotal + $shipping['valor'];

/* ================= CRIAR PEDIDO ================= */

$stmt=$pdo->prepare("
INSERT INTO orders(user_id,situacao,subtotal,shipping,total,payment_status,created_at)
VALUES(?, 'novo', ?, ?, ?, 'pending', NOW())
");
$stmt->execute([
    $userId,
    $subtotal,
    $shipping['valor'],
    $total
]);

$orderId=$pdo->lastInsertId();

/* ================= ITENS ================= */

$stmtItem=$pdo->prepare("
INSERT INTO order_items(order_id,variant_id,qtd,preco_unitario)
VALUES(?,?,?,?)
");

foreach($cart as $item){
    $stmtItem->execute([
        $orderId,
        $item['variant_id'],
        $item['quantity'],
        $item['preco']
    ]);
}

/* ================= ENDEREÇO ================= */

$stmt=$pdo->prepare("
INSERT INTO order_addresses
(order_id,nome,telefone,cep,rua,numero,complemento,bairro,cidade,estado)
VALUES(?,?,?,?,?,?,?,?,?,?)
");
$stmt->execute([
$orderId,
$dados['nome'],
$dados['telefone'],
$dados['cep'],
$dados['rua'],
$dados['numero'],
$dados['complemento'],
$dados['bairro'],
$dados['cidade'],
$dados['estado']
]);

/* ================= GERAR PAGAMENTO ================= */

$pref = criarPreferenciaMP($orderId, $total);

if(empty($pref['id']) || empty($pref['init_point'])){
    throw new Exception("Erro ao criar pagamento");
}

/* salva id do pagamento externo */
$pdo->prepare("UPDATE orders SET mp_preference_id=? WHERE id=?")
    ->execute([$pref['id'],$orderId]);

/* ================= LIMPA CARRINHO ================= */

$pdo->prepare("DELETE FROM cart_items WHERE user_id=?")->execute([$userId]);

$pdo->commit();

/* ================= REDIRECT PARA MERCADO PAGO ================= */

header("Location: ".$pref['init_point']);
exit;

}catch(Exception $e){

$pdo->rollBack();

flash('error','Erro ao iniciar pagamento');
header("Location: /ultraware_gaming/public/checkout.php");
exit;

}



case "calcular_frete":

    header('Content-Type: application/json');

    try{

        require_once __DIR__."/app/helpers/auth.php";
        requireLogin();

        require_once __DIR__."/app/helpers/shipping.php";

        $data = json_decode(file_get_contents("php://input"), true);

        if(!$data || empty($data['cep'])){
            echo json_encode(["erro"=>"cep_invalido"]);
            exit;
        }

        $cep = preg_replace('/[^0-9]/','',$data['cep']);

        $frete = calculateShipping($cep);

        if(!$frete){
            echo json_encode(["erro"=>"frete_nao_disponivel"]);
            exit;
        }

        echo json_encode([
            "nome"=>$frete['nome'],
            "valor"=>(float)$frete['valor'],
            "prazo"=>(int)$frete['prazo']
        ]);

    }catch(Throwable $e){

        echo json_encode([
            "erro"=>"internal_error",
            "debug"=>$e->getMessage()
        ]);

    }

exit;



case 'update_name':

require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";

$id = $_SESSION['user']['id'];

$nome = trim($_POST['nome']);
$senha = $_POST['senha'];

$stmt = $pdo->prepare("SELECT senha FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if(!$user || !password_verify($senha, $user['senha'])){
    die("Senha incorreta");
}

$stmt = $pdo->prepare("UPDATE users SET nome=? WHERE id=?");
$stmt->execute([$nome,$id]);

$_SESSION['user']['nome']=$nome;

header("Location: /ultraware_gaming/public/account.php");
exit;



case 'update_email':

require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";

$id = $_SESSION['user']['id'];

$email = trim($_POST['email']);
$senha = $_POST['senha'];

$stmt = $pdo->prepare("SELECT senha FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if(!$user || !password_verify($senha, $user['senha'])){
    die("Senha incorreta");
}

$stmt = $pdo->prepare("UPDATE users SET email=? WHERE id=?");
$stmt->execute([$email,$id]);

$_SESSION['user']['email']=$email;

header("Location: /ultraware_gaming/public/account.php");
exit;



case 'update_password':

require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";

$id = $_SESSION['user']['id'];

$atual = $_POST['senha_atual'];
$nova  = $_POST['nova_senha'];

$stmt = $pdo->prepare("SELECT senha FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if(!$user || !password_verify($atual, $user['senha'])){
    die("Senha atual incorreta");
}

$hash = password_hash($nova,PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET senha=? WHERE id=?");
$stmt->execute([$hash,$id]);

header("Location: /ultraware_gaming/public/account.php");
exit;



case 'delete_account':

require_once ROOT."/app/config/database.php";
require_once ROOT."/app/helpers/session.php";

$id = $_SESSION['user']['id'];

$stmt=$pdo->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

session_destroy();

header("Location: /ultraware_gaming/public/index.php");
exit;

}
