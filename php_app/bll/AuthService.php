<?php
// BLL — Authentication Service
require_once __DIR__ . '/../dal/UserDAL.php';

class AuthService {
    private UserDAL $userDAL;

    public function __construct() {
        $this->userDAL = new UserDAL();
    }

    /** Tenta autenticar. Devolve dados do utilizador ou null. */
    public function login(string $email, string $password): ?array {
        $u = $this->userDAL->findByEmail($email);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            return null;
        }
        return [
            'id'       => (int)$u['id'],
            'name'     => $u['name'],
            'email'    => $u['email'],
            'role'     => $u['role'],
            'store_id' => $u['store_id'],
            'cat_id'   => $u['cat_id'],
        ];
    }

    /** Regista um novo consumidor. Devolve [success, id, error]. */
    public function register(string $name, string $email, string $phone, string $password): array {
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            return [false, 0, 'Preencha todos os campos correctamente (palavra-passe ≥ 6 caracteres).'];
        }
        if ($this->userDAL->findByEmail($email)) {
            return [false, 0, 'Email já registado.'];
        }
        $id = $this->userDAL->insert([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => 'consumer',
            'phone'         => $phone ?: null,
        ]);
        return [true, $id, null];
    }

    /** Devolve URL de destino consoante o role. */
    public function dashboardUrlFor(string $role): string {
        return [
            'admin'         => '/admin',
            'store_manager' => '/store',
            'cat'           => '/cat',
            'consumer'      => '/account',
        ][$role] ?? '/';
    }
}
