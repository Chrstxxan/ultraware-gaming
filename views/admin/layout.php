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

<title>UltraWare Admin</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    theme: {
        extend: {
            colors:{ primary:'#3b82f6' },
            borderRadius:{ uw:'22px' }
        }
    }
}
</script>

</head>

<body class="bg-zinc-950 text-white min-h-screen">

<!-- ================= MOBILE TOPBAR ================= -->
<div class="md:hidden fixed inset-x-0 top-5 z-50 flex justify-center px-4">
    <div class="w-full max-w-7xl">
        <div class="bg-zinc-900/70 backdrop-blur-2xl border border-white/10
        rounded-full px-4 h-12 flex items-center justify-between">
            <span class="font-semibold text-primary">UltraWare Admin</span>

            <button onclick="toggleAdminMenu()"
            class="p-2 rounded-full hover:bg-white/10 text-xl leading-none">
                ⋮
            </button>
        </div>
    </div>
</div>

<!-- ================= MOBILE DRAWER ================= -->
<div id="adminMenu" class="md:hidden fixed inset-0 z-[70] hidden">

    <div id="adminMenuBg"
    onclick="toggleAdminMenu()"
    class="absolute inset-0 bg-black/40 backdrop-blur-md opacity-0 transition"></div>

    <div id="adminMenuPanel"
    class="absolute right-0 top-0 h-full w-[85%] p-6
        translate-x-full transition duration-300
        bg-white/5 backdrop-blur-3xl
        border-l border-white/20
        shadow-[0_0_60px_rgba(0,0,0,0.8)]
        rounded-l-[28px] overflow-hidden">

        <button onclick="toggleAdminMenu()" class="mb-8 text-xl">✕</button>

        <nav class="flex flex-col gap-5 text-lg">
            <a href="/ultraware_gaming/public/admin/orders.php" class="px-4 py-2 rounded-xl hover:bg-white/10">Pedidos</a>
            <a href="/ultraware_gaming/public/admin/create_product.php" class="px-4 py-2 rounded-xl hover:bg-white/10">Cadastrar produto</a>
            <a href="/ultraware_gaming/routes.php?action=logout" class="px-4 py-2 rounded-xl text-red-400 hover:bg-white/10">Sair</a>
        </nav>
    </div>
</div>

<div class="min-h-screen">

<!-- ================= DESKTOP SIDEBAR ================= -->
<aside class="hidden md:flex md:flex-col md:fixed md:left-0 md:top-0 md:h-screen md:w-64
bg-zinc-900 border-r border-zinc-800 p-6 space-y-6 z-40">

    <h1 class="text-xl font-bold text-primary">UltraWare Admin</h1>

    <nav class="space-y-3 text-sm">
        <a href="/ultraware_gaming/public/admin/orders.php" class="block hover:text-primary">Pedidos</a>
        <a href="/ultraware_gaming/public/admin/create_product.php" class="block hover:text-primary">Cadastrar produto</a>
        <a href="/ultraware_gaming/routes.php?action=logout" class="block text-red-400 hover:text-red-300">Sair</a>
    </nav>

</aside>

<!-- ================= CONTEÚDO ================= -->
<main class="w-full md:pl-64 pt-24 md:pt-10 px-4 md:px-10">

<?php require_once ROOT . "/views/components/toast.php"; ?>