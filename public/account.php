<?php
require_once "../app/config/path.php";
require_once ROOT."/app/helpers/session.php";
require_once ROOT."/app/config/database.php";

if(!isLogged()){
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user']['id'];

$stmt=$pdo->prepare("SELECT nome,email FROM users WHERE id=?");
$stmt->execute([$id]);
$user=$stmt->fetch();

require_once ROOT."/views/layout/header.php";
?>

<!-- Alpine para modais -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="min-h-[70vh] flex items-center justify-center" x-data="{sec:null,logout:false}">

<div class="w-full max-w-2xl relative
bg-white/5 backdrop-blur-3xl
border border-white/20
shadow-[0_0_60px_rgba(0,0,0,0.8)]
rounded-[28px] p-8 space-y-8
before:absolute before:inset-0 before:pointer-events-none
before:bg-gradient-to-b before:from-white/20 before:via-white/5 before:to-transparent
before:opacity-40 before:rounded-[28px]">

    <h1 class="text-3xl font-semibold text-center">Minha Conta</h1>

    <!-- NOME -->
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-zinc-400 text-sm">Nome</p>
                <p class="text-lg"><?= htmlspecialchars($user['nome']) ?></p>
            </div>
            <button @click="sec='nome'" class="text-primary hover:underline">Alterar</button>
        </div>

        <form x-show="sec==='nome'" method="POST" action="/ultraware_gaming/routes.php?action=update_name" class="space-y-3">
            <input name="nome" required placeholder="Novo nome"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <input type="password" name="senha" required placeholder="Senha atual"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <button class="bg-primary px-5 py-2 rounded-full">Salvar</button>
        </form>
    </div>

    <!-- EMAIL -->
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-zinc-400 text-sm">Email</p>
                <p class="text-lg"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <button @click="sec='email'" class="text-primary hover:underline">Alterar</button>
        </div>

        <form x-show="sec==='email'" method="POST" action="/ultraware_gaming/routes.php?action=update_email" class="space-y-3">
            <input type="email" name="email" required placeholder="Novo email"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <input type="password" name="senha" required placeholder="Senha atual"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <button class="bg-primary px-5 py-2 rounded-full">Salvar</button>
        </form>
    </div>

    <!-- SENHA -->
    <div class="space-y-2">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-zinc-400 text-sm">Senha</p>
                <p class="text-lg">••••••••</p>
            </div>
            <button @click="sec='senha'" class="text-primary hover:underline">Alterar</button>
        </div>

        <form x-show="sec==='senha'" method="POST" action="/ultraware_gaming/routes.php?action=update_password" class="space-y-3">
            <input type="password" name="senha_atual" required placeholder="Senha atual"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <input type="password" name="nova_senha" required placeholder="Nova senha"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <button class="bg-primary px-5 py-2 rounded-full">Salvar</button>
        </form>
    </div>

    <!-- AÇÕES -->
    <div class="flex justify-between items-center pt-6 border-t border-white/10">

        <button @click="logout=true" class="text-blue-400 hover:text-blue-300 transition">
            Sair da conta
        </button>

        <button @click="sec='delete'"
        class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-full">
            Excluir conta
        </button>

    </div>

</div>


<!-- MODAL LOGOUT -->
<div x-show="logout" class="fixed inset-0 z-[200] flex items-center justify-center">

    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div class="relative bg-white/5 backdrop-blur-3xl border border-white/20
    rounded-[28px] p-8 w-[90%] max-w-md text-center space-y-6">

        <h2 class="text-2xl font-semibold">Sair da conta?</h2>
        <p class="text-zinc-400">Você precisará fazer login novamente</p>

        <div class="flex gap-4 justify-center">
            <button @click="logout=false"
            class="px-6 py-2 rounded-full border border-white/20 hover:bg-white/10">
                Cancelar
            </button>

            <a href="/ultraware_gaming/routes.php?action=logout"
            class="px-6 py-2 rounded-full bg-blue-500 hover:bg-blue-400">
                Confirmar
            </a>
        </div>

    </div>
</div>

<!-- MODAL DELETE ACCOUNT -->
<div x-show="sec==='delete'" class="fixed inset-0 z-[200] flex items-center justify-center">

    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div class="relative bg-white/5 backdrop-blur-3xl border border-white/20
    rounded-[28px] p-8 w-[90%] max-w-md text-center space-y-6">

        <h2 class="text-2xl font-semibold text-red-400">Excluir conta</h2>

        <p class="text-zinc-400 text-sm">
            Essa ação é permanente.<br>
            Todos os seus dados e pedidos serão removidos.
        </p>

        <form method="POST" action="/ultraware_gaming/routes.php?action=delete_account" class="space-y-4">

            <input type="password" name="senha" required placeholder="Digite sua senha para confirmar"
            class="w-full px-4 py-2 rounded-full bg-black/30 border border-white/10">

            <div class="flex gap-4 justify-center pt-2">

                <button type="button" @click="sec=null"
                class="px-6 py-2 rounded-full border border-white/20 hover:bg-white/10">
                    Cancelar
                </button>

                <button class="px-6 py-2 rounded-full bg-red-500 hover:bg-red-600">
                    Confirmar exclusão
                </button>

            </div>

        </form>

    </div>
</div>

</div>

<?php require_once ROOT."/views/layout/footer.php"; ?>
