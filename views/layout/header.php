<?php
require_once ROOT . "/app/helpers/session.php";
require_once ROOT."/app/helpers/flash.php";
require_once ROOT."/app/config/database.php";

/* CATEGORIAS PARA NAVBAR */
$stmt = $pdo->query("SELECT id,nome FROM categories ORDER BY ordem,nome");
$navCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>UltraWare Gaming</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#3b82f6'
            },
            borderRadius: {
                uw: '22px'
            }
        }
    }
}
</script>

<!-- FIX DROPDOWN HOVER -->
<style>
.nav-open > .dropdown-menu{
    opacity:1!important;
    transform:translateY(0)!important;
    pointer-events:auto!important;
}
</style>

</head>

<body class="bg-zinc-950 text-white pt-[110px] md:pt-28">

<!-- NAVBAR FLUTUANTE -->
<header class="fixed top-5 left-1/2 -translate-x-1/2 z-50 w-full flex justify-center pointer-events-none">

    <div class="pointer-events-auto
    w-[92%] max-w-5xl mx-3 md:mx-0
    bg-zinc-900/65 backdrop-blur-3xl
    border border-white/10
    shadow-[0_25px_80px_rgba(0,0,0,0.65)]
    rounded-full px-3">

        <div class="px-6 py-3 flex items-center justify-between">

            <!-- ESQUERDA -->
            <div class="flex items-center gap-4">

                <!-- CATEGORIAS -->
                <div class="relative hidden md:block"
                onmouseenter="this.classList.add('nav-open')"
                onmouseleave="this.classList.remove('nav-open')">

                    <button class="px-4 py-2 rounded-full hover:bg-white/10 transition flex items-center gap-2">
                        Categorias
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-70"
                        viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.937a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- DROPDOWN -->
                    <div class="
                        dropdown-menu
                        absolute top-full -left-2 pt-3 px-2
                        opacity-0 translate-y-2 pointer-events-none
                        transition duration-200
                    ">

                        <!-- ZONA DE SEGURANÇA DO MOUSE -->
                        <div class="absolute -top-4 left-0 w-full h-4"></div>

                        <div class="
                            w-64 p-3 rounded-uw
                            bg-zinc-900/70 backdrop-blur-3xl
                            border border-white/10
                            shadow-[0_25px_80px_rgba(0,0,0,0.65)]
                        ">

                            <?php foreach($navCategories as $cat): ?>
                                <a href="/ultraware_gaming/public/?categoria=<?= $cat['id'] ?>"
                                class="block px-4 py-2 rounded-xl hover:bg-white/10 transition">
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </a>
                            <?php endforeach; ?>

                        </div>

                    </div>
                </div>

                <!-- LOGO -->
                <a href="/ultraware_gaming/public/" class="flex items-center gap-3">
                    <img src="/ultraware_gaming/public/assets/img/logo.png" class="h-8 opacity-90">
                </a>

            </div>

            <!-- BUSCA -->
            <div class="hidden md:flex flex-1 mx-8">
                <input type="text"
                placeholder="Buscar produtos..."
                class="w-full bg-black/30 border border-white/10 rounded-full px-5 py-2
                focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
            </div>

            <!-- AÇÕES -->
            <div class="flex items-center gap-2 text-sm">

                <?php if(isLogged()): ?>

                    <a href="/ultraware_gaming/public/account.php"
                    class="px-3 py-1.5 rounded-full hover:bg-white/10 transition">
                        Conta
                    </a>

                    <a href="/ultraware_gaming/public/meus_pedidos.php"
                    class="px-3 py-1.5 rounded-full hover:bg-white/10 transition">
                        Pedidos
                    </a>

                    <a href="/ultraware_gaming/routes.php?action=logout"
                    class="px-3 py-1.5 rounded-full hover:bg-red-500/20 text-red-300 transition">
                        Sair
                    </a>

                <?php else: ?>

                    <a href="/ultraware_gaming/public/login.php"
                    class="px-4 py-1.5 rounded-full bg-primary hover:bg-blue-500 transition font-medium">
                        Entrar
                    </a>

                <?php endif; ?>

                <!-- CARRINHO -->
                <?php
                require_once ROOT."/app/helpers/cart_db.php";

                $count = isLogged()
                    ? cartCountDB($pdo,$_SESSION['user']['id'])
                    : 0;
                ?>

                <a href="/ultraware_gaming/public/cart.php"
                class="relative p-2 rounded-full hover:bg-white/10 transition">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.6" stroke="currentColor" class="w-6 h-6 text-zinc-200">
                        <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386a1.125 1.125 0 011.11.894l.383 1.916m0 0L6.75 14.25a2.25 2.25 0 002.205 1.875h7.59a2.25 2.25 0 002.205-1.875l1.495-7.44a1.125 1.125 0 00-1.11-1.341H5.129zM9 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm7.5 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>

                    <span id="cartCount"
                    class="absolute -top-1 -right-1 bg-primary text-[10px] px-1.5 py-0.5 rounded-full <?= $count ? '' : 'hidden' ?>">
                        <?= $count ?>
                    </span>

                </a>

            </div>

        </div>
    </div>

</header>

<?php if($f = getFlash()): ?>

<div id="toast" class="fixed top-28 left-1/2 -translate-x-1/2 z-[999]">
    <div class="
        px-6 py-4 rounded-uw
        bg-zinc-900/80 backdrop-blur-2xl
        border border-white/10
        shadow-[0_20px_60px_rgba(0,0,0,0.7)]
        text-center min-w-[280px]
        <?= $f['type']==='error'?'text-red-300':'text-green-300' ?>
    ">
        <?= htmlspecialchars($f['message']) ?>
    </div>
</div>

<script>
setTimeout(()=>{
    document.getElementById('toast')?.remove()
},3500);
</script>

<?php endif; ?>

<main class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-10">
