<?php
require_once "../app/config/path.php";
require_once ROOT."/views/layout/header.php";
?>

<div class="min-h-[70vh] flex items-center justify-center">

    <div class="w-full max-w-md glass-card p-8">

        <form method="POST" action="/ultraware_gaming/routes.php?action=login" class="space-y-6">

            <div class="text-center space-y-2 mb-6">
                <h1 class="text-3xl font-semibold">Entrar</h1>
                <p class="text-zinc-400 text-sm">Acesse sua conta para continuar</p>
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Email</label>
                <input type="email" name="email" required
                class="w-full px-5 py-3 rounded-full
                bg-white/5 backdrop-blur-xl
                border border-white/15
                focus:border-primary focus:ring-0
                transition placeholder:text-zinc-400">
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Senha</label>
                <input type="password" name="senha" required
                class="w-full px-5 py-3 rounded-full
                bg-white/5 backdrop-blur-xl
                border border-white/15
                focus:border-primary focus:ring-0
                transition placeholder:text-zinc-400">
            </div>

            <div class="flex justify-between text-sm text-zinc-400">
                <a href="/ultraware_gaming/public/register.php" class="hover:text-primary transition">Criar conta</a>
                <a href="/ultraware_gaming/public/forgot.php" class="hover:text-primary transition">Esqueci minha senha</a>
            </div>

            <button type="submit"
            class="w-full bg-primary hover:bg-blue-500
            text-white py-3 rounded-full font-semibold
            transition
            shadow-[0_15px_40px_rgba(59,130,246,0.45)]
            hover:shadow-[0_20px_55px_rgba(59,130,246,0.65)]">
                Entrar
            </button>

        </form>

    </div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>
