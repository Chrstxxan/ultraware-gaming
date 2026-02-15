<?php

require_once __DIR__ . "/app/config/database.php";
require_once __DIR__ . "/app/helpers/session.php";

$action = $_GET['action'] ?? null;

if ($action === "login"){
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])){
        $_SESSION['user'] = $user;
        header("Location: /ultraware_gaming/public");
        exit;
    }else {
        echo "Login invalido";
    }
}

if ($action === "store_product") {

    if (!$_SESSION['user']['admin']) {
        die("Acesso negado");
    }

    // 1️⃣ cria produto base
    $stmt = $pdo->prepare("
        INSERT INTO products (nome, descricao, categoria)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $_POST['nome'],
        $_POST['descricao'],
        $_POST['categoria']
    ]);

    $productId = $pdo->lastInsertId();


    // 2️⃣ percorre todas as variações
    if(!empty($_POST['variants'])){

        foreach ($_POST['variants'] as $index => $variant) {

            // ignora variação vazia
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


            // 3️⃣ upload das imagens da variação
            $inputName = "variant_images_" . $index;

            if (!empty($_FILES[$inputName]['name'][0])) {

                foreach ($_FILES[$inputName]['tmp_name'] as $k => $tmp) {

                    if(!$tmp) continue;

                    $ext = pathinfo($_FILES[$inputName]['name'][$k], PATHINFO_EXTENSION);
                    $name = uniqid() . "." . $ext;

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
}

if ($action === "logout") {
    session_destroy();
    header("Location: /ultraware_gaming/public");
    exit;
}
