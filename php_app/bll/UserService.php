<?php
// BLL — User management (admin) + settings
require_once __DIR__ . '/../dal/UserDAL.php';
require_once __DIR__ . '/../dal/MiscDAL.php';

class UserService {
    private UserDAL $userDAL;
    public function __construct() { $this->userDAL = new UserDAL(); }

    public function listForAdmin(): array { return $this->userDAL->allWithStoreCat(); }

    public function save(int $id, array $input): array {
        $name  = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $role  = in_array($input['role'] ?? 'consumer', ['consumer','store_manager','cat','admin'], true) ? $input['role'] : 'consumer';
        $store = (int)($input['store_id'] ?? 0) ?: null;
        $cat   = (int)($input['cat_id']   ?? 0) ?: null;
        $pw    = $input['password'] ?? '';

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) return [false, 'Dados inválidos.'];

        if ($id) {
            $data = ['name'=>$name, 'email'=>$email, 'role'=>$role, 'store_id'=>$store, 'cat_id'=>$cat];
            if ($pw) $data['password_hash'] = password_hash($pw, PASSWORD_BCRYPT);
            $this->userDAL->update($id, $data);
            return [true, 'Utilizador actualizado.'];
        }
        if (strlen($pw) < 6) return [false, 'Palavra-passe ≥ 6 caracteres.'];
        $this->userDAL->insert([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($pw, PASSWORD_BCRYPT),
            'role'          => $role,
            'store_id'      => $store,
            'cat_id'        => $cat,
        ]);
        return [true, 'Utilizador criado.'];
    }
}

class SettingsService {
    private SettingDAL $dal;
    public function __construct() { $this->dal = new SettingDAL(); }

    public function getAll(): array { return $this->dal->all(); }
    public function save(array $input): void {
        $this->dal->set('warranty_alert_days', (string)(int)($input['warranty_alert_days'] ?? 30));
        $this->dal->set('shipping_flat_cost',  (string)(float)($input['shipping_flat_cost']  ?? 5.90));
    }
}
