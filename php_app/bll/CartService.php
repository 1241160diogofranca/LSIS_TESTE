<?php
// BLL — Cart Service (gere o carrinho na sessão; não usa DAL)
require_once __DIR__ . '/../dal/ProductDAL.php';
require_once __DIR__ . '/../dal/PartDAL.php';

class CartService {
    private ProductDAL $productDAL;
    private PartDAL $partDAL;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $this->productDAL = new ProductDAL();
        $this->partDAL    = new PartDAL();
    }

    public function getAll(): array { return $_SESSION['cart']; }

    public function count(): int {
        $n = 0; foreach ($_SESSION['cart'] as $i) $n += (int)$i['qty']; return $n;
    }

    public function subtotal(): float {
        $s = 0.0; foreach ($_SESSION['cart'] as $i) $s += (float)$i['price'] * (int)$i['qty']; return $s;
    }

    /** Adiciona um item; valida que existe e calcula chave única. */
    public function add(string $type, int $id, int $qty): array {
        $qty = max(1, $qty);
        $item = $type === 'part'
            ? $this->partDAL->findById($id)
            : $this->productDAL->findById($id);
        if (!$item) return [false, 'Item não encontrado.'];

        $key = $type . '_' . $id;
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$key] = [
                'key'   => $key,
                'type'  => $type,
                'id'    => (int)$item['id'],
                'name'  => $item['name'],
                'price' => (float)$item['price'],
                'image' => $item['image_url'],
                'qty'   => $qty,
            ];
        }
        return [true, $item['name'] . ' adicionado ao carrinho.'];
    }

    public function update(string $key, int $qty): void {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['qty'] = max(1, $qty);
        }
    }

    public function remove(string $key): void { unset($_SESSION['cart'][$key]); }

    public function clear(): void { $_SESSION['cart'] = []; }
}
