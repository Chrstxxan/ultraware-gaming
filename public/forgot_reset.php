<?php
require_once "../app/config/path.php";
require_once ROOT."/app/helpers/session.php";

if(!isset($_SESSION['reset_user'])){
    header("Location: login.php");
    exit;
}

require_once ROOT."/views/layout/header.php";
?>

<div class="min-h-[70vh] flex items-center justify-center">

    <div class="w-full max-w-md glass-card p-8">

        <form method="POST" action="/ultraware_gaming/routes.php?action=forgot_reset" class="space-y-6">

            <div class="text-center space-y-2 mb-6">
                <h1 class="text-3xl font-semibold">Nova senha</h1>
                <p class="text-zinc-400 text-sm">Digite sua nova senha</p>
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Nova senha</label>
                <input type="password" name="senha" required
                class="w-full px-5 py-3 rounded-full bg-white/5 border border-white/15 focus:border-primary">
            </div>

            <div class="space-y-2">
                <label class="text-sm text-zinc-400">Confirmar senha</label>
                <input type="password" name="confirmar" required
                class="w-full px-5 py-3 rounded-full bg-white/5 border border-white/15 focus:border-primary">
            </div>

            <button type="submit"
            class="w-full bg-primary hover:bg-blue-500 text-white py-3 rounded-full font-semibold transition">
                Alterar senha
            </button>

        </form>

    </div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>