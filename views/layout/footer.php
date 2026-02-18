</main>

<footer class="border-t border-zinc-800 mt-16 py-10 text-center text-zinc-500 text-sm">
    © <?= date('Y') ?> UltraWare Gaming — Todos os direitos reservados
</footer>
<div id="globalToast"
class="fixed top-24 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none"></div>

<script>
function showToast(message,type="success"){

    const toast = document.createElement("div");

    toast.className = `
        px-6 py-4 rounded-uw
        backdrop-blur-3xl border border-white/10
        shadow-[0_20px_60px_rgba(0,0,0,0.7)]
        mb-3 text-center min-w-[280px]
        ${type==="error" ? "text-red-300 bg-red-500/10" : "text-green-300 bg-emerald-500/10"}
        opacity-0 translate-y-4 transition duration-300
    `;

    toast.innerText = message;

    const container = document.getElementById("globalToast");
    container.appendChild(toast);

    setTimeout(()=>toast.classList.remove("opacity-0","translate-y-4"),10);
    setTimeout(()=>{
        toast.classList.add("opacity-0","translate-y-4");
        setTimeout(()=>toast.remove(),300);
    },2800);
}
</script>

<script>
function toggleCategoryPanel(){
    const panel = document.getElementById('catPanel');
    if(!panel) return;
    panel.classList.toggle('hidden');
}
</script>

</body>
</html>
