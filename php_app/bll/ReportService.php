<?php
// BLL — Report Service (KPIs e relatórios)
require_once __DIR__ . '/../dal/UserDAL.php';
require_once __DIR__ . '/../dal/ProductDAL.php';
require_once __DIR__ . '/../dal/OrderDAL.php';
require_once __DIR__ . '/../dal/OrderItemDAL.php';
require_once __DIR__ . '/../dal/ServiceTicketDAL.php';

class ReportService {
    private UserDAL $userDAL;
    private ProductDAL $productDAL;
    private OrderDAL $orderDAL;
    private OrderItemDAL $itemDAL;
    private ServiceTicketDAL $tixDAL;

    public function __construct() {
        $this->userDAL    = new UserDAL();
        $this->productDAL = new ProductDAL();
        $this->orderDAL   = new OrderDAL();
        $this->itemDAL    = new OrderItemDAL();
        $this->tixDAL     = new ServiceTicketDAL();
    }

    public function adminKpis(): array {
        return [
            'users'      => $this->userDAL->count(),
            'products'   => $this->productDAL->count(),
            'orders'     => $this->orderDAL->count(),
            'revenue'    => $this->orderDAL->totalRevenuePaid(),
            'open_tix'   => $this->tixDAL->count("status IN ('open','assigned','in_progress')"),
            'closed_tix' => $this->tixDAL->count("status='closed'"),
            'unassigned' => $this->tixDAL->count("cat_id IS NULL AND status='open'"),
        ];
    }

    public function revenueMonths(int $n = 6): array       { return $this->orderDAL->revenueByMonth($n); }
    public function revenueByCategory(): array              { return $this->itemDAL->revenueByCategory(); }
    public function ticketsByStatus(): array                { return $this->tixDAL->countByStatusGroup(); }
    public function catPerformance(): array                 { return $this->tixDAL->catPerformance(); }
}
