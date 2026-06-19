<?php
// Initialise database + seed data. Idempotent: only seeds when tables are empty.
require_once __DIR__ . '/db.php';

function run_init_db(): array {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $root = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $sql = <<<'SQL'
CREATE DATABASE IF NOT EXISTS meireles_connect
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE meireles_connect;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('consumer','store_manager','cat','admin') NOT NULL DEFAULT 'consumer',
  phone VARCHAR(30) NULL,
  address TEXT NULL,
  store_id INT NULL,
  cat_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  city VARCHAR(100) NOT NULL,
  address TEXT NULL,
  phone VARCHAR(30) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  city VARCHAR(100) NOT NULL,
  phone VARCHAR(30) NULL,
  email VARCHAR(190) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  icon VARCHAR(80) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  brand VARCHAR(100) NOT NULL DEFAULT 'Meireles',
  model VARCHAR(120) NOT NULL,
  category_id INT NOT NULL,
  description TEXT,
  specs TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(500),
  manual_url VARCHAR(500),
  warranty_months INT NOT NULL DEFAULT 24,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS parts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  compatible_models TEXT,
  image_url VARCHAR(500)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  store_id INT NULL,
  status ENUM('pending_payment','paid','processing','shipped','in_transit','delivered','cancelled') NOT NULL DEFAULT 'pending_payment',
  payment_status ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  shipping_address TEXT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  shipping_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_type ENUM('product','part') NOT NULL DEFAULT 'product',
  product_id INT NULL,
  part_id INT NULL,
  name VARCHAR(200) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS warranties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  serial_number VARCHAR(120) NOT NULL,
  purchase_date DATE NOT NULL,
  expiry_date DATE NOT NULL,
  proof_filename VARCHAR(255) NULL,
  status ENUM('active','expired','pending_doc') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS service_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NULL,
  warranty_id INT NULL,
  cat_id INT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('open','assigned','in_progress','awaiting_parts','closed','cancelled') NOT NULL DEFAULT 'open',
  priority ENUM('low','normal','high') NOT NULL DEFAULT 'normal',
  diagnosis TEXT NULL,
  intervention TEXT NULL,
  parts_used TEXT NULL,
  photo_filename VARCHAR(255) NULL,
  opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  closed_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  type VARCHAR(60) NOT NULL DEFAULT 'info',
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(120) NOT NULL,
  details TEXT NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  k VARCHAR(80) PRIMARY KEY,
  v VARCHAR(255) NOT NULL
) ENGINE=InnoDB;
SQL;
    $root->exec($sql);

    $db = get_db();

    // Seed categories
    $count = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count === 0) {
        $cats = [
            ['Fogões', 'fogoes', 'flame'],
            ['Placas', 'placas', 'square-half'],
            ['Fornos', 'fornos', 'oven'],
            ['Exaustores', 'exaustores', 'fan'],
            ['Máquinas de Lavar', 'maquinas-lavar', 'washing-machine'],
        ];
        $st = $db->prepare("INSERT INTO categories (name, slug, icon) VALUES (?,?,?)");
        foreach ($cats as $c) $st->execute($c);
    }

    // Seed stores
    if ((int)$db->query("SELECT COUNT(*) FROM stores")->fetchColumn() === 0) {
        $rows = [
            ['Meireles Porto Centro', 'Porto', 'Rua Santa Catarina 200', '+351 22 000 0001'],
            ['Meireles Lisboa Avenida', 'Lisboa', 'Av. Liberdade 50', '+351 21 000 0001'],
            ['Meireles Braga', 'Braga', 'Rua do Souto 15', '+351 25 000 0001'],
        ];
        $st = $db->prepare("INSERT INTO stores (name, city, address, phone) VALUES (?,?,?,?)");
        foreach ($rows as $r) $st->execute($r);
    }

    // Seed CATs
    if ((int)$db->query("SELECT COUNT(*) FROM cats")->fetchColumn() === 0) {
        $rows = [
            ['CAT Norte', 'Porto', '+351 22 999 1111', 'norte@meireles.pt'],
            ['CAT Centro', 'Coimbra', '+351 23 999 2222', 'centro@meireles.pt'],
            ['CAT Sul', 'Lisboa', '+351 21 999 3333', 'sul@meireles.pt'],
        ];
        $st = $db->prepare("INSERT INTO cats (name, city, phone, email) VALUES (?,?,?,?)");
        foreach ($rows as $r) $st->execute($r);
    }

    // Seed users
    if ((int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn() === 0) {
        $users = [
            ['Administrador Meireles', 'admin@meireles.pt',   'admin123',   'admin',         null, null, null],
            ['Loja Porto Centro',      'loja@meireles.pt',    'loja123',    'store_manager', null, 1,    null],
            ['Técnico CAT Norte',      'cat@meireles.pt',     'cat123',     'cat',           null, null, 1],
            ['Ana Cliente',            'cliente@meireles.pt', 'cliente123', 'consumer',      '+351 919 000 000', null, null],
        ];
        $st = $db->prepare("INSERT INTO users (name,email,password_hash,role,phone,store_id,cat_id) VALUES (?,?,?,?,?,?,?)");
        foreach ($users as $u) {
            $hash = password_hash($u[2], PASSWORD_BCRYPT);
            $st->execute([$u[0],$u[1],$hash,$u[3],$u[4],$u[5],$u[6]]);
        }
    }

    // Seed products
    if ((int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn() === 0) {
        $catId = function($slug) use ($db) {
            $s = $db->prepare("SELECT id FROM categories WHERE slug=?");
            $s->execute([$slug]); return (int)$s->fetchColumn();
        };
        $prods = [
            ['FGM-E730X','Fogão Inox 5 Bicos E730X','Meireles','E730X', $catId('fogoes'),
              'Fogão a gás com 5 bicos, forno multifunções e acabamento inox premium.',
              "5 queimadores; Forno multifunções 110L; Largura 90cm; Classe energética A+",
              649.90, 18, 'https://images.unsplash.com/photo-1556909114-44e3e9399eaa?w=800', 24],
            ['FGM-G203W','Fogão Branco 4 Bicos G203W','Meireles','G203W', $catId('fogoes'),
              'Fogão compacto, ideal para cozinhas pequenas, 4 bicos a gás.',
              "4 queimadores; Forno 60L; Largura 50cm",
              289.00, 25, 'https://images.unsplash.com/photo-1564540586988-aa4e53c3d799?w=800', 24],
            ['PLC-IND60','Placa Indução 60cm IND60','Meireles','IND60', $catId('placas'),
              'Placa de indução com 4 zonas, comandos touch e booster.',
              "4 zonas indução; Touch control; 7400W; Booster",
              399.00, 14, 'https://images.unsplash.com/photo-1556909212-d5b604d0c90d?w=800', 24],
            ['FRN-MX80','Forno Multifunções MX80','Meireles','MX80', $catId('fornos'),
              'Forno multifunções 80L com display LCD e pirólise.',
              "80L; 10 funções; LCD; Pirólise",
              549.00, 9, 'https://images.unsplash.com/photo-1585664811087-47f65abbad64?w=800', 36],
            ['EXA-CL90','Exaustor Chaminé CL90','Meireles','CL90', $catId('exaustores'),
              'Exaustor de chaminé inox 90cm, 3 velocidades + booster.',
              "90cm; 700m³/h; 3 velocidades; LED",
              219.00, 30, 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=800', 24],
            ['EXA-TL60','Exaustor Telescópico TL60','Meireles','TL60', $catId('exaustores'),
              'Exaustor telescópico encastrável 60cm.',
              "60cm; 450m³/h; 2 motores",
              129.00, 40, 'https://images.unsplash.com/photo-1556909102-f6d7e1b2a08e?w=800', 24],
            ['MLR-W812','Máquina Lavar Roupa W812','Meireles','W812', $catId('maquinas-lavar'),
              'Máquina de lavar roupa 8kg, 1200 rpm, classe A.',
              "8kg; 1200rpm; 14 programas; A",
              429.00, 12, 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=800', 36],
            ['MLR-W914','Máquina Lavar Roupa W914','Meireles','W914', $catId('maquinas-lavar'),
              'Máquina de lavar roupa 9kg, 1400 rpm, inverter motor.',
              "9kg; 1400rpm; Inverter; Vapor",
              579.00, 7, 'https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?w=800', 36],
        ];
        $st = $db->prepare("INSERT INTO products (sku,name,brand,model,category_id,description,specs,price,stock,image_url,warranty_months) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        foreach ($prods as $p) $st->execute($p);
    }

    // Seed parts
    if ((int)$db->query("SELECT COUNT(*) FROM parts")->fetchColumn() === 0) {
        $parts = [
            ['PRT-QM01','Queimador Médio Universal','Queimador de substituição para fogões a gás.',12.50, 50, 'E730X, G203W', 'https://images.unsplash.com/photo-1581092334597-94d3c4232e6e?w=600'],
            ['PRT-FLT1','Filtro Carbono Exaustor','Filtro de carbono ativado, compatível com CL90 / TL60.',19.90, 80, 'CL90, TL60', 'https://images.unsplash.com/photo-1581092580497-e9e5c8e09f3a?w=600'],
            ['PRT-VID1','Vidro Forno MX80','Vidro interior de substituição para forno MX80.',49.00, 15, 'MX80', 'https://images.unsplash.com/photo-1585664811087-47f65abbad64?w=600'],
            ['PRT-MTR1','Motor Inverter W914','Motor inverter de substituição.',189.00, 6, 'W914', 'https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?w=600'],
        ];
        $st = $db->prepare("INSERT INTO parts (sku,name,description,price,stock,compatible_models,image_url) VALUES (?,?,?,?,?,?,?)");
        foreach ($parts as $p) $st->execute($p);
    }

    // Seed settings
    if ((int)$db->query("SELECT COUNT(*) FROM settings")->fetchColumn() === 0) {
        $st = $db->prepare("INSERT INTO settings (k,v) VALUES (?,?)");
        $st->execute(['warranty_alert_days', '30']);
        $st->execute(['shipping_flat_cost', '5.90']);
    }

    return ['ok' => true];
}

if (php_sapi_name() === 'cli' || (isset($argv) && in_array('--cli', $argv ?? []))) {
    run_init_db();
    echo "DB initialised\n";
}
