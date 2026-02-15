<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>UltraWare Gaming</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    @keyframes fadeIn {
        from { opacity:0; transform:translateY(10px); }
        to { opacity:1; transform:translateY(0); }
    }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6'
                    }
                }
            }
        }
    </script>

</head>

<body class="bg-zinc-900 text-zinc-100">

<header class="bg-zinc-950 border-b border-zinc-800 px-8 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-primary">UltraWare Gaming</h1>

    <nav class="space-x-6">
        <a href="/ultraware_gaming/public" class="hover:text-primary">Loja</a>
        <a href="/ultraware_gaming/routes.php?action=logout" class="text-red-400 hover:text-red-300">Sair</a>
    </nav>
</header>

<main class="max-w-4xl mx-auto py-10 px-6">
