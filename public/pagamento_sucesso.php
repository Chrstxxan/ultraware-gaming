<?php
require_once "../app/config/database.php";
require_once "../app/helpers/session.php";

$orderId = $_GET['external_reference'] ?? null;
$status = null;

if($orderId){
    $stmt=$pdo->prepare("SELECT payment_status FROM orders WHERE id=?");
    $stmt->execute([$orderId]);
    $status=$stmt->fetchColumn();
}
?>

<h1>Pagamento recebido 🎉</h1>

<?php if($status === 'paid'): ?>
<p>Seu pagamento foi aprovado com sucesso.</p>
<p>Já estamos preparando seu pedido.</p>

<a href="/ultraware_gaming/public/meus_pedidos.php">
Ver meus pedidos
</a>

<?php else: ?>
<p>Estamos aguardando a confirmação do pagamento.</p>
<p>Atualizando automaticamente...</p>

<script>
setTimeout(()=>location.reload(),3000);
</script>
<?php endif; ?>
