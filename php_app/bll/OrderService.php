<?php
// BLL — Order Service: checkout, pagamento, gestão de estados
require_once __DIR__ . '/../dal/OrderDAL.php';
require_once __DIR__ . '/../dal/OrderItemDAL.php';
require_once __DIR__ . '/../dal/UserDAL.php';
require_once __DIR__ . '/CartService.php';
require_once __DIR__ . '/NotificationService.php';

class OrderService {
    const SHIPPING_FLAT = 5.90;

    private OrderDAL $orderDAL;
    private OrderItemDAL $itemDAL;
    private NotificationService $notif;

    public function __construct() {
        $this->orderDAL = new OrderDAL();
        $this->itemDAL  = new OrderItemDAL();
        $this->notif    = new NotificationService();
    }

    /** Cria encomenda a partir do carrinho em sessão. */
    public function checkout(int $userId, string $address, array $cart): array {
        if (empty($cart))   return [false, 0, 'Carrinho vazio.'];
        if (!$address)      return [false, 0, 'Morada de entrega obrigatória.'];

        $subtotal = 0;
        foreach ($cart as $i) $subtotal += (float)$i['price'] * (int)$i['qty'];
        $shipping = self::SHIPPING_FLAT;
        $total    = $subtotal + $shipping;

        $db = get_db();
        $db->beginTransaction();
        try {
            $orderId = $this->orderDAL->insert([
                'user_id'          => $userId,
                'status'           => 'pending_payment',
                'payment_status'   => 'unpaid',
                'shipping_address' => $address,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shipping,
                'total'            => $total,
            ]);
            foreach ($cart as $i) {
                $this->itemDAL->insert([
                    'order_id'   => $orderId,
                    'item_type'  => $i['type'],
                    'product_id' => $i['type'] === 'product' ? $i['id'] : null,
                    'part_id'    => $i['type'] === 'part'    ? $i['id'] : null,
                    'name'       => $i['name'],
                    'unit_price' => $i['price'],
                    'quantity'   => $i['qty'],
                    'line_total' => (float)$i['price'] * (int)$i['qty'],
                ]);
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            return [false, 0, 'Erro a criar a encomenda: ' . $e->getMessage()];
        }
        $this->notif->send($userId, 'Encomenda #' . $orderId . ' criada', 'Aguarda pagamento. Total: ' . number_format($total,2,',','.') . ' €');
        return [true, $orderId, 'Encomenda criada.'];
    }

    /** Pagamento simulado: troca estado para "paid". */
    public function pay(int $orderId, int $userId): array {
        $o = $this->orderDAL->findByIdAndUser($orderId, $userId);
        if (!$o) return [false, 'Encomenda não encontrada.'];
        if ($o['payment_status'] === 'paid') return [false, 'Já paga.'];

        $this->orderDAL->updatePayment($orderId, 'paid', 'paid');
        $this->notif->send($userId, 'Pagamento confirmado', "Encomenda #{$orderId} paga. Será expedida em breve.");
        return [true, 'Pagamento confirmado (simulado).'];
    }

    public function updateStatusByAdmin(int $orderId, string $status): array {
        $allowed = ['pending_payment','paid','processing','shipped','in_transit','delivered','cancelled'];
        if (!in_array($status, $allowed, true)) return [false, 'Estado inválido.'];

        $this->orderDAL->updateStatus($orderId, $status);
        $o = $this->orderDAL->findById($orderId);
        if ($o) $this->notif->send((int)$o['user_id'], "Atualização da encomenda #{$orderId}", "Novo estado: {$status}");
        return [true, 'Encomenda actualizada.'];
    }

    public function listForUser(int $userId): array          { return $this->orderDAL->findByUser($userId); }
    public function recentForUser(int $userId, int $n = 5): array { return $this->orderDAL->findByUser($userId, $n); }
    public function orderWithItemsForUser(int $orderId, int $userId): ?array {
        $o = $this->orderDAL->findByIdAndUser($orderId, $userId);
        if (!$o) return null;
        return ['order' => $o, 'items' => $this->itemDAL->findByOrder($orderId)];
    }
    public function allForAdmin(): array                     { return $this->orderDAL->allWithClient(); }
    public function recentForAdmin(int $n = 6): array        { return $this->orderDAL->recentWithClient($n); }
    public function itemCount(int $orderId): int             { return $this->itemDAL->countByOrder($orderId); }
}
