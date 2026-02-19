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

<div id="live-toast"
class="fixed z-[9999]
left-1/2 -translate-x-1/2 bottom-6
md:left-auto md:translate-x-0 md:right-6 md:top-6 md:bottom-auto
opacity-0 pointer-events-none transition-all duration-300">

    <div class="bg-zinc-900 border border-primary text-white
    px-6 py-4 rounded-2xl shadow-2xl
    flex items-center gap-4">

        <div class="w-2 h-10 bg-primary rounded-full"></div>

        <div>
            <p class="font-semibold">Sucesso</p>
            <p id="toast-text" class="text-sm text-zinc-300"></p>
        </div>

    </div>
</div>

<script>
window.showToast = function(msg){

    const t = document.getElementById("live-toast");
    if(!t) return;

    document.getElementById("toast-text").textContent = msg;

    t.classList.remove("opacity-0");
    t.classList.add("opacity-100");

    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(()=>{
        t.classList.remove("opacity-100");
        t.classList.add("opacity-0");
    },2400);
}
</script>

<script>
window.updateCartBadge = function(count){

    const el = document.getElementById("cart-count");
    if(!el) return;

    el.textContent = count;

    if(count>0){
        el.classList.remove("hidden");
    }else{
        el.classList.add("hidden");
    }

    // micro animação
    el.parentElement.classList.add("scale-110");
    setTimeout(()=>el.parentElement.classList.remove("scale-110"),150);
}
</script>

</body>
</html>
