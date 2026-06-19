<?php
// BLL — Service Ticket Service (assistência técnica)
require_once __DIR__ . '/../dal/ServiceTicketDAL.php';
require_once __DIR__ . '/../dal/UserDAL.php';
require_once __DIR__ . '/NotificationService.php';

class ServiceTicketService {
    private ServiceTicketDAL $tixDAL;
    private UserDAL $userDAL;
    private NotificationService $notif;

    public function __construct() {
        $this->tixDAL  = new ServiceTicketDAL();
        $this->userDAL = new UserDAL();
        $this->notif   = new NotificationService();
    }

    public function listForUser(int $userId): array        { return $this->tixDAL->findByUserWithProduct($userId); }
    public function recentForUser(int $userId, int $n = 5): array { return $this->tixDAL->findRecentByUser($userId, $n); }
    public function findForUser(int $id, int $userId): ?array     { return $this->tixDAL->findByIdAndUser($id, $userId); }
    public function listForCat(int $catId): array          { return $this->tixDAL->findByCat($catId); }
    public function recentForCat(int $catId, int $n = 8): array { return $this->tixDAL->findByCat($catId, $n); }
    public function findForCat(int $id, int $catId): ?array { return $this->tixDAL->findByCatAndId($id, $catId); }
    public function allForAdmin(): array                    { return $this->tixDAL->allWithClient(); }
    public function recentForAdmin(int $n = 6): array       { return $this->tixDAL->recentWithClient($n); }

    /** Cria novo pedido de assistência. */
    public function open(int $userId, string $title, string $description, ?int $productId, string $priority, ?string $photoFilename): array {
        if (!$title || !$description) return [false, 0, 'Título e descrição são obrigatórios.'];
        if (!in_array($priority, ['low','normal','high'], true)) $priority = 'normal';

        $id = $this->tixDAL->insert([
            'user_id'        => $userId,
            'product_id'     => $productId,
            'title'          => $title,
            'description'    => $description,
            'priority'       => $priority,
            'photo_filename' => $photoFilename,
        ]);
        $this->notif->send($userId, 'Pedido de assistência aberto', "Pedido #{$id} criado. Será atribuído a um CAT em breve.");
        foreach ($this->userDAL->findByRole('admin') as $a) {
            $this->notif->send((int)$a['id'], 'Novo pedido de assistência', "#{$id} aguarda atribuição.", 'alert');
        }
        return [true, $id, 'Pedido de assistência criado com sucesso.'];
    }

    /** Actualização pelo CAT — diagnóstico + intervenção + estado. */
    public function updateByCat(int $ticketId, int $catId, array $input): array {
        $t = $this->tixDAL->findByCatAndId($ticketId, $catId);
        if (!$t) return [false, 'Pedido não encontrado ou sem permissão.'];

        $allowed = ['assigned','in_progress','awaiting_parts','closed','cancelled'];
        $status = in_array($input['status'] ?? '', $allowed, true) ? $input['status'] : $t['status'];

        $closedAt = $t['closed_at'];
        if ($status === 'closed' && !$closedAt) $closedAt = date('Y-m-d H:i:s');

        $this->tixDAL->updateIntervention($ticketId, [
            'diagnosis'    => $input['diagnosis']    ?? $t['diagnosis'],
            'intervention' => $input['intervention'] ?? $t['intervention'],
            'parts_used'   => $input['parts_used']   ?? $t['parts_used'],
            'status'       => $status,
            'closed_at'    => $closedAt,
        ]);
        $this->notif->send((int)$t['user_id'], "Atualização do pedido #{$ticketId}", "Estado: {$status}");
        return [true, 'Pedido actualizado.'];
    }

    /** Atribuição pelo Admin. */
    public function assignToCat(int $ticketId, ?int $catId): void {
        $this->tixDAL->assignToCat($ticketId, $catId);
        if ($catId) {
            foreach ($this->userDAL->findByRoleAndCat('cat', $catId) as $u) {
                $this->notif->send((int)$u['id'], 'Novo pedido atribuído', "#{$ticketId} foi atribuído ao seu CAT.", 'alert');
            }
        }
    }

    public function kpisForCat(int $catId): array {
        $tixDAL = $this->tixDAL;
        return [
            'total'    => $tixDAL->count('cat_id = ?', [$catId]),
            'open'     => $tixDAL->countByCatAndStatus($catId, ['assigned','in_progress','awaiting_parts']),
            'closed'   => $tixDAL->countByCatAndStatus($catId, ['closed']),
            'avg_hours'=> round((float)($tixDAL->averageResolutionHoursByCat($catId) ?? 0), 1),
        ];
    }
}
