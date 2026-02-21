<?php
require_once "../app/config/path.php";
require_once ROOT."/views/layout/header.php";
?>

<div class="min-h-[70vh] flex items-center justify-center">

    <div class="w-full max-w-md glass-card p-8">

        <form method="POST" action="/ultraware_gaming/routes.php?action=forgot_check" class="space-y-6">

            <div class="text-center space-y-2 mb-6">
                <h1 class="text-3xl font-semibold">Recuperar senha</h1>
                <p class="text-zinc-400 text-sm">Informe o email da sua conta</p>
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Email</label>
                <input type="email" name="email" required
                class="w-full px-5 py-3 rounded-full
                bg-white/5 backdrop-blur-xl
                border border-white/15
                focus:border-primary transition">
            </div>

            <button type="submit"
            class="w-full bg-primary hover:bg-blue-500 text-white py-3 rounded-full font-semibold transition">
                Continuar
            </button>

            <div class="text-center text-sm text-zinc-400">
                <a href="login.php" class="hover:text-primary">Voltar ao login</a>
            </div>

        </form>

    </div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>