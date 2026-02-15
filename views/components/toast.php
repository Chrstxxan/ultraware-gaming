<?php if(isset($_GET['success'])): ?>

<div id="toast"
class="fixed z-50
left-1/2 -translate-x-1/2 bottom-6
md:left-auto md:translate-x-0 md:right-6 md:top-6 md:bottom-auto
bg-zinc-900 border border-primary text-white
px-6 py-4 rounded-2xl shadow-2xl
flex items-center gap-4
animate-[fadeIn_.25s_ease]">

    <div class="w-2 h-10 bg-primary rounded-full"></div>

    <div>
        <p class="font-semibold">Sucesso</p>
        <p class="text-sm text-zinc-300">
            Produto cadastrado com sucesso!
        </p>
    </div>

</div>

<script>
setTimeout(() => {
    const el = document.getElementById('toast');
    if(el){
        el.style.opacity = "0";
        el.style.transform += " translateY(10px)";
        setTimeout(()=>el.remove(), 300);
    }
}, 2600);
</script>

<?php endif; ?>
