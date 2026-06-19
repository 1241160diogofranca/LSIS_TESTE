<?php
// BLL — Warranty Service
require_once __DIR__ . '/../dal/WarrantyDAL.php';
require_once __DIR__ . '/../dal/ProductDAL.php';
require_once __DIR__ . '/NotificationService.php';

class WarrantyService {
    private WarrantyDAL $warDAL;
    private ProductDAL $productDAL;
    private NotificationService $notif;

    public function __construct() {
        $this->warDAL     = new WarrantyDAL();
        $this->productDAL = new ProductDAL();
        $this->notif      = new NotificationService();
    }

    public function listForUser(int $userId): array { return $this->warDAL->findByUserWithProduct($userId); }
    public function recentForUser(int $userId, int $n = 5): array { return $this->warDAL->findRecentByUser($userId, $n); }

    /** Activa uma garantia. Calcula data de expiração a partir do produto. */
    public function activate(int $userId, int $productId, string $serial, string $purchaseDate, ?string $proofFilename = null): array {
        if (!$productId || !$serial || !$purchaseDate) {
            return [false, 'Todos os campos são obrigatórios.'];
        }
        $p = $this->productDAL->findById($productId);
        if (!$p) return [false, 'Produto inexistente.'];

        $months = (int)$p['warranty_months'];
        $expiry = date('Y-m-d', strtotime($purchaseDate . " +{$months} months"));
        $status = $proofFilename ? 'active' : 'pending_doc';

        $this->warDAL->insert([
            'user_id'        => $userId,
            'product_id'     => $productId,
            'serial_number'  => $serial,
            'purchase_date'  => $purchaseDate,
            'expiry_date'    => $expiry,
            'proof_filename' => $proofFilename,
            'status'         => $status,
        ]);
        $this->notif->send($userId, 'Garantia activada', "Garantia para s/n {$serial} válida até {$expiry}.");
        return [true, $proofFilename ? 'Garantia activada com sucesso.' : 'Garantia registada (aguarda anexo de prova de compra).'];
    }
}
