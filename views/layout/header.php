    <?php
    require_once ROOT . "/app/helpers/session.php";
    require_once ROOT."/app/helpers/flash.php";
    require_once ROOT."/app/config/database.php";
    require_once ROOT."/app/helpers/cart_db.php";

    /* CATEGORIAS PARA NAVBAR */
    $stmt = $pdo->query("SELECT id,nome FROM categories ORDER BY ordem,nome");
    $navCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = isLogged()
        ? cartCountDB($pdo,$_SESSION['user']['id'])
        : 0;
    ?>

    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UltraWare Gaming</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { primary: '#3b82f6' },
                borderRadius: { uw: '22px' }
            }
        }
    }
    </script>

    <style>
    .nav-open > .dropdown-menu{
        opacity:1!important;
        transform:translateY(0)!important;
        pointer-events:auto!important;
    }

    .dock-btn{
        width:46px;
        height:46px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:16px;
        background:rgba(24,24,27,.55);
        backdrop-filter:blur(22px);
        border:1px solid rgba(255,255,255,.08);
        transition:.25s;
    }
    .dock-btn:hover{
        background:rgba(59,130,246,.25);
        transform:translateY(-2px) scale(1.05);
    }
    
    .glass-card{
    background:rgba(255,255,255,.05);
    backdrop-filter:blur(28px);
    border:1px solid rgba(255,255,255,.20);
    box-shadow:0 0 60px rgba(0,0,0,.8);
    border-radius:28px;
    position:relative;
    overflow:hidden;
    }

    .glass-card::before{
        content:"";
        position:absolute;
        inset:0;
        pointer-events:none;
        background:linear-gradient(to bottom,
            rgba(255,255,255,.20),
            rgba(255,255,255,.05),
            transparent);
        opacity:.35;
    }
    
    .action-btn{
    padding:10px 18px;
    border-radius:999px;
    font-size:14px;
    transition:.2s;
    }

    .action-btn.blue{
    background:#3b82f6;
    }

    .action-btn.blue:hover{
    background:#2563eb;
    }

    .action-btn.red{
    background:#ef4444;
    }

    .action-btn.red:hover{
    background:#dc2626;
    }
    
    </style>
    </head>

    <body class="bg-zinc-950 text-white pt-28">

    <header class="fixed inset-x-0 top-5 z-50 flex justify-center px-4">

    <div class="w-full max-w-7xl space-y-2 relative">

        <!-- ================= DESKTOP TOPBAR ================= -->
        <div class="hidden md:grid bg-zinc-900/70 backdrop-blur-2xl border border-white/10 rounded-full px-6 h-11
                    grid-cols-[1fr_auto_1fr] items-center">

            <!-- esquerda -->
            <div class="flex items-center gap-4 justify-self-start">

                <div class="relative"
                onmouseenter="this.classList.add('nav-open')"
                onmouseleave="this.classList.remove('nav-open')">

                    <button class="px-4 py-1.5 rounded-full hover:bg-white/10 transition flex items-center gap-2">
                        Categorias
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-70" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.937a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z"/>
                        </svg>
                    </button>

                    <div class="dropdown-menu absolute top-full pt-3 opacity-0 translate-y-2 pointer-events-none transition">
                        <div class="w-64 p-3 rounded-uw bg-zinc-900/90 border border-white/10">
                            <?php foreach($navCategories as $cat): ?>
                                <a href="/ultraware_gaming/public/?categoria=<?= $cat['id'] ?>"
                                class="block px-4 py-2 rounded-xl hover:bg-white/10 transition">
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- logo -->
            <div class="flex justify-center">
                <a href="/ultraware_gaming/public/">
                    <img src="/ultraware_gaming/public/assets/img/logo.png" class="h-8">
                </a>
            </div>

            <!-- busca -->
            <div class="flex items-center gap-3 justify-self-end">

                <div class="w-[260px]">
                    <input type="text" placeholder="Buscar produtos..."
                    class="w-full bg-black/30 border border-white/10 rounded-full px-4 py-1.5
                    focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                </div>

                <?php if(!isLogged()): ?>
                    <a href="/ultraware_gaming/public/login.php"
                    class="bg-primary hover:bg-blue-500 px-5 py-1.5 rounded-full
                    shadow-[0_10px_30px_rgba(59,130,246,.45)] transition">
                        Entrar
                    </a>
                <?php endif; ?>

            </div>

        </div>


        <!-- ================= MOBILE BAR ================= -->
        <div class="md:hidden bg-zinc-900/70 backdrop-blur-2xl border border-white/10
                    rounded-full px-4 h-12 flex items-center justify-between">

            <!-- logo -->
            <a href="/ultraware_gaming/public/">
                <img src="/ultraware_gaming/public/assets/img/logo.png" class="h-8">
            </a>

            <div class="flex items-center gap-2">

                <?php if(!isLogged()): ?>
                    <a href="/ultraware_gaming/public/login.php"
                    class="bg-primary px-4 py-1 rounded-full text-sm shadow-lg">
                        Entrar
                    </a>
                <?php endif; ?>

                <!-- busca -->
                <button onclick="toggleSearchMobile()" class="p-2 rounded-full hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 20l-3.5-3.5"/>
                    </svg>
                </button>

                <!-- menu 3 pontos -->
                <button onclick="toggleMobileMenu()" class="p-2 rounded-full hover:bg-white/10 text-xl leading-none">
                    ⋮
                </button>

            </div>
        </div>


        <!-- ================= DOCK ================= -->
        <div class="absolute right-0 top-[58px] md:-right-14 md:top-[-7px]
                flex flex-col items-end gap-2">

            <!-- carrinho -->
            <a href="/ultraware_gaming/public/cart.php" class="dock-btn relative">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"
                fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.5l1.72 10.32a2.25 2.25 0 002.22 1.93h7.78a2.25 2.25 0 002.22-1.93L20.25 6H6.12"/>
                    <circle cx="9" cy="20" r="1.25"/>
                    <circle cx="18" cy="20" r="1.25"/>
                </svg>

                <span id="cart-count"
                class="absolute -top-1 -right-1 bg-primary text-[10px] px-1.5 rounded-full <?= $count ? '' : 'hidden' ?>">
                <?= $count ?>
                </span>
            </a>

            <?php if(isLogged()): ?>

            <!-- conta -->
            <a href="/ultraware_gaming/public/account.php" class="dock-btn pointer-events-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                    <circle cx="12" cy="8" r="3.25"/>
                    <path d="M4.5 19c1.8-3.8 13.2-3.8 15 0"/>
                </svg>
            </a>

            <!-- pedidos -->
            <a href="/ultraware_gaming/public/meus_pedidos.php" class="dock-btn pointer-events-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 8.25l-9-4.5-9 4.5m18 0v7.5a2.25 2.25 0 01-1.13 1.95L12 21.75l-7.87-4.05A2.25 2.25 0 013 15.75v-7.5m18 0L12 12.75 3 8.25"/>
                </svg>
            </a>

            <?php endif; ?>

        </div>

    </div>
    </header>


    <!-- SEARCH OVERLAY -->
    <div id="mobileSearch"
    class="md:hidden fixed inset-x-0 top-[22px] px-4 z-[60] hidden">

        <div class="bg-zinc-900/80 backdrop-blur-2xl border border-white/10
                    rounded-full px-4 h-12 flex items-center gap-3">

            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 opacity-70"
            fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <circle cx="11" cy="11" r="7"/>
                <path d="M20 20l-3.5-3.5"/>
            </svg>

            <input type="text" placeholder="Buscar produtos..."
            class="flex-1 bg-transparent outline-none text-sm">

            <button onclick="toggleSearchMobile()" class="text-zinc-400 text-lg">✕</button>

        </div>
    </div>


    <!-- MOBILE MENU -->
    <div id="mobileMenu"
    class="md:hidden fixed inset-0 z-[70] hidden">

        <!-- backdrop -->
        <div id="menuBg"
        onclick="toggleMobileMenu()"
        class="absolute inset-0 bg-black/40 backdrop-blur-md opacity-0 transition"></div>

        <!-- painel -->
        <div id="menuPanel"
        class="absolute right-0 top-0 h-full w-[85%] p-6
            translate-x-full transition duration-300
            bg-white/5 backdrop-blur-3xl
            border-l border-white/20
            shadow-[0_0_60px_rgba(0,0,0,0.8)]
            rounded-l-[28px] overflow-hidden
            before:absolute before:inset-0 before:pointer-events-none
            before:bg-gradient-to-b before:from-white/20 before:via-white/5 before:to-transparent
            before:opacity-40 before:rounded-l-[28px]">

            <button onclick="toggleMobileMenu()" class="mb-6 text-xl">✕</button>

            <div class="flex flex-col gap-5 text-lg">
                <?php foreach($navCategories as $cat): ?>
                    <a href="/ultraware_gaming/public/?categoria=<?= $cat['id'] ?>"
                    class="block px-4 py-2 rounded-xl hover:bg-white/10 active:bg-white/15 transition">
                        <?= htmlspecialchars($cat['nome']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>


    <script>
    function toggleMobileMenu(){
        const panel = document.getElementById('menuPanel');
        const bg = document.getElementById('menuBg');
        const wrapper = document.getElementById('mobileMenu');

        const opening = wrapper.classList.contains('hidden');

        if(opening){
            wrapper.classList.remove('hidden');
            requestAnimationFrame(()=>{
                panel.classList.remove('translate-x-full');
                bg.classList.remove('opacity-0');
            });
        }else{
            panel.classList.add('translate-x-full');
            bg.classList.add('opacity-0');
            setTimeout(()=>wrapper.classList.add('hidden'),300);
        }
    }

    function toggleSearchMobile(){
        document.getElementById('mobileSearch')?.classList.toggle('hidden');
    }
    </script>

<script>
window.updateCartCounter = function(count){

    const badge = document.getElementById("cart-count");
    if(!badge) return;

    badge.textContent = count;

    if(count > 0)
        badge.classList.remove("hidden");
    else
        badge.classList.add("hidden");

    // animação igual botões dock
    badge.parentElement.classList.add("scale-110");
    setTimeout(()=>badge.parentElement.classList.remove("scale-110"),180);
}
</script>

    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 py-10">
