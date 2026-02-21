</main>
</div>

<script>
function toggleAdminMenu(){
    const panel=document.getElementById('adminMenuPanel');
    const bg=document.getElementById('adminMenuBg');
    const wrap=document.getElementById('adminMenu');

    const open=wrap.classList.contains('hidden');

    if(open){
        wrap.classList.remove('hidden');
        requestAnimationFrame(()=>{
            panel.classList.remove('translate-x-full');
            bg.classList.remove('opacity-0');
        });
    }else{
        panel.classList.add('translate-x-full');
        bg.classList.add('opacity-0');
        setTimeout(()=>wrap.classList.add('hidden'),300);
    }
}
</script>

</body>
</html>