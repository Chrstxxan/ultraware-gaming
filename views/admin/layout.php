<?php
require_once ROOT . "/app/helpers/session.php";
require_once ROOT."/app/helpers/auth.php";

if (!isLogged() || !user()['admin']) {
    requireLogin();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Painel Admin - UltraWare</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#3b82f6'
            },
            borderRadius: {
                uw: '18px'
            }
        }
    }
}
</script>

</head>

<body class="bg-zinc-950 text-white">

<div class="flex min-h-screen">

    <!-- MENU LATERAL -->
    <aside class="w-60 bg-zinc-900 border-r border-zinc-800 p-6 space-y-6">

        <h1 class="text-xl font-bold text-primary">UltraWare Admin</h1>

        <nav class="space-y-3 text-sm">

            <a href="/ultraware_gaming/public/admin/create_product.php"
            class="block hover:text-primary">Cadastrar produto</a>

            <a href="/ultraware_gaming/public/admin/orders.php"
            class="block hover:text-primary">Pedidos</a>

            <a href="/ultraware_gaming/routes.php?action=logout"
            class="block text-red-400 hover:text-red-300">Sair</a>

        </nav>

    </aside>

    <!-- CONTEÚDO -->
    <main class="flex-1 p-10">
