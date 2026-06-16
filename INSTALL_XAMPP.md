# Meireles Connect — Instalação no XAMPP

Guia passo-a-passo para correr o portal num ambiente XAMPP local (Windows / macOS / Linux).

---

## 1. Pré-requisitos

- **XAMPP** instalado (https://www.apachefriends.org/) — versão com **PHP 8.0 ou superior**.
- O XAMPP traz já incluído: Apache + MariaDB/MySQL + PHP + phpMyAdmin.

---

## 2. Copiar o código para o htdocs

1. Copie a pasta **`php_app/`** inteira para dentro de `htdocs` do XAMPP, **renomeando-a** para o nome que quiser usar como URL.  
   - **Windows**: `C:\xampp\htdocs\meireles\`
   - **macOS**: `/Applications/XAMPP/xamppfiles/htdocs/meireles/`
   - **Linux**: `/opt/lampp/htdocs/meireles/`

A estrutura final fica:

```
htdocs/
└── meireles/
    ├── .htaccess
    ├── index.php
    ├── router.php          (não usado no Apache — só serve para PHP built-in server)
    ├── config/
    ├── controllers/
    ├── views/
    ├── lib/
    ├── assets/
    └── uploads/            (criar se não existir, com permissões de escrita)
```

> Garanta que a pasta `uploads/` existe e é gravável (no Linux/Mac: `chmod -R 755 uploads/`).

---

## 3. Arrancar Apache + MySQL

Abra o **XAMPP Control Panel** e clique em **Start** nos módulos:
- **Apache**
- **MySQL** (ou MariaDB)

---

## 4. Criar a Base de Dados

### Opção A — via phpMyAdmin (interface gráfica)

1. Vá a http://localhost/phpmyadmin
2. Clique em **Importar** → escolha o ficheiro `config/schema.sql`
3. Clique em **Executar**. Isto cria a BD `meireles_connect` e todas as tabelas.

### Opção B — via linha de comando

```bash
# Windows (cmd):
C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\meireles\config\schema.sql

# macOS / Linux:
/opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/meireles/config/schema.sql
```

---

## 5. Configurar credenciais da BD

No XAMPP por defeito, o utilizador MySQL é **`root`** com **palavra-passe vazia**.  
O ficheiro `config/db.php` já tem esses defaults — não é preciso alterar nada se mantiver as definições standard do XAMPP.

Se quiser alterar (recomendado em produção), edite **`config/db.php`** e ajuste:

```php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'meireles_connect';
$user = getenv('DB_USER') ?: 'root';        // ← altere aqui
$pass = getenv('DB_PASS') ?: '';             // ← altere aqui
```

---

## 6. Popular dados de demonstração (opcional)

Abra um browser e visite **uma única vez**:

```
http://localhost/meireles/
```

Na primeira visita, o `index.php` executa o `init_db.php` automaticamente e cria:
- 5 categorias (Fogões, Placas, Fornos, Exaustores, Máquinas de Lavar)
- 8 produtos demo
- 4 peças
- 4 utilizadores demo (ver tabela abaixo)
- 3 lojas + 3 CAT

Para dados extra (encomendas/garantias/tickets de demonstração), execute na linha de comando:

```bash
# Windows:
C:\xampp\php\php.exe C:\xampp\htdocs\meireles\seed_demo.php

# macOS / Linux:
/opt/lampp/bin/php /opt/lampp/htdocs/meireles/seed_demo.php
```

---

## 7. Aceder ao portal

Abra o browser em:

```
http://localhost/meireles/
```

---

## 8. Contas de demonstração

| Perfil | Email | Palavra-passe |
|---|---|---|
| **Administrador** | admin@meireles.pt | admin123 |
| **Loja** | loja@meireles.pt | loja123 |
| **CAT** | cat@meireles.pt | cat123 |
| **Cliente** | cliente@meireles.pt | cliente123 |

Após o login, cada perfil é redirecionado para a sua área:
- Admin → `/admin`
- Loja → `/store`
- CAT → `/cat`
- Cliente → `/account`

---

## 9. Resolução de problemas comuns

### ❌ "Page not found" ao clicar em /catalog ou /login
**Causa**: O mod_rewrite do Apache não está activo.

**Solução**:
1. Edite `C:\xampp\apache\conf\httpd.conf`
2. Descomente a linha: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Encontre o bloco `<Directory "C:/xampp/htdocs">` e mude `AllowOverride None` para **`AllowOverride All`**
4. Reinicie o Apache

### ❌ "SQLSTATE[HY000] [2002] No such file or directory"
**Causa**: O MySQL não está a correr.

**Solução**: Inicie o módulo MySQL no XAMPP Control Panel.

### ❌ "Access denied for user 'root'@'localhost'"
**Causa**: A palavra-passe do `root` não é vazia.

**Solução**: Edite `config/db.php` e ponha a palavra-passe correcta.

### ❌ Erro 500 ao fazer upload de fotos/faturas
**Causa**: A pasta `uploads/warranties` ou `uploads/tickets` não existe ou não tem permissões.

**Solução**: Crie estas pastas manualmente:
```
uploads/
├── warranties/
└── tickets/
```
E garanta permissões de escrita (no Linux: `chmod -R 755 uploads/`).

### ❌ Sessões não persistem entre páginas
**Causa**: Pasta de sessões do PHP sem permissões.

**Solução** (Linux/Mac apenas): `chmod 777 /opt/lampp/temp/`.

---

## 10. Estrutura técnica resumida

| Componente | Tecnologia | Porto/Caminho |
|---|---|---|
| Servidor web | Apache (XAMPP) | http://localhost:80 |
| Base de dados | MariaDB/MySQL | localhost:3306 |
| Linguagem | PHP 8+ | — |
| Frontend | HTML5 + CSS3 puro + JavaScript vanilla | — |
| Ícones | Phosphor Icons (CDN) | — |
| Tipografia | Inter + Outfit (Google Fonts CDN) | — |

A aplicação **não tem dependências externas** (sem Composer, sem npm). Funciona out-of-the-box num XAMPP standard.

---

## 11. Subdiretório vs domínio próprio

Se quiser que o site corra em **`http://localhost/`** (raiz) em vez de **`http://localhost/meireles/`**:

- Mova o conteúdo de `meireles/` directamente para `htdocs/`
- OU configure um VirtualHost no `httpd-vhosts.conf` apontando para a pasta

Não é preciso alterar nenhum link nos ficheiros PHP — todos usam paths absolutos (`/catalog`, `/login`, etc.) que funcionam em ambos os cenários se o site for servido na raiz.
