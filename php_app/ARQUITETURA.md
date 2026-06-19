# Arquitetura em 3 camadas — Meireles Connect

Este projeto está organizado segundo o padrão clássico **DAL / BLL / PL** (3-Tier Architecture).

```
┌──────────────────────────────────────────────────────────┐
│  PL — Presentation Layer                                 │
│  controllers/*.php   →  recebem o pedido HTTP, validam   │
│  views/*.php         →  renderizam HTML                  │
│  lib/helpers.php     →  funções utilitárias de PL        │
└──────────────────────────┬───────────────────────────────┘
                           │ chama métodos
                           ▼
┌──────────────────────────────────────────────────────────┐
│  BLL — Business Logic Layer                              │
│  bll/AuthService.php          (login, registo)           │
│  bll/ProductService.php       (catálogo, filtros)        │
│  bll/CartService.php          (carrinho de sessão)       │
│  bll/OrderService.php         (checkout, pagamento)      │
│  bll/WarrantyService.php      (ativação garantias)       │
│  bll/ServiceTicketService.php (assistência técnica)      │
│  bll/NotificationService.php  (alertas in-app)           │
│  bll/ReportService.php        (KPIs e relatórios)        │
│  bll/UserService.php          (gestão utilizadores)      │
└──────────────────────────┬───────────────────────────────┘
                           │ chama métodos
                           ▼
┌──────────────────────────────────────────────────────────┐
│  DAL — Data Access Layer                                 │
│  dal/BaseDAL.php             (classe abstracta CRUD)     │
│  dal/UserDAL.php                                         │
│  dal/ProductDAL.php                                      │
│  dal/CategoryDAL.php                                     │
│  dal/PartDAL.php                                         │
│  dal/OrderDAL.php                                        │
│  dal/OrderItemDAL.php                                    │
│  dal/WarrantyDAL.php                                     │
│  dal/ServiceTicketDAL.php                                │
│  dal/MiscDAL.php  (Notification, Log, Store, Cat,        │
│                    Setting)                              │
└──────────────────────────┬───────────────────────────────┘
                           │ PDO + SQL
                           ▼
                  ┌─────────────────┐
                  │  MariaDB / MySQL│
                  └─────────────────┘
```

## Regras de comunicação

1. **As views NUNCA chamam DAL diretamente** — só recebem dados já processados pelo controlador via BLL.
2. **Os controladores NUNCA fazem SQL** — toda a lógica de dados está no BLL/DAL.
3. **A BLL NUNCA escreve SQL** — usa sempre o DAL para falar com a BD.
4. **O DAL NUNCA implementa regras de negócio** — só persiste, consulta e mapeia dados.

## Exemplo concreto — fluxo de "Ativar Garantia"

| Camada | Ficheiro | Responsabilidade |
|---|---|---|
| **PL (view)** | `views/account/warranties.php` | Mostra o formulário com `<select>` de produtos |
| **PL (controller)** | `controllers/warranties.php :: activate()` | Lê `$_POST`, verifica CSRF, faz upload do ficheiro, chama o serviço |
| **BLL** | `bll/WarrantyService.php :: activate()` | Valida campos, busca `warranty_months` do produto, calcula `expiry_date = purchase_date + meses`, decide `status`, manda inserir, envia notificação |
| **DAL (escrita)** | `dal/WarrantyDAL.php :: insert()` | Executa `INSERT INTO warranties (...)` |
| **DAL (leitura aux.)** | `dal/ProductDAL.php :: findById()` | `SELECT * FROM products WHERE id=?` |
| **BD** | tabela `warranties` | Persistência |

## Vantagens académicas/práticas desta arquitetura

- **Testabilidade**: cada DAL e BLL pode ser testada isoladamente.
- **Manutenção**: trocar de PDO para Eloquent só obriga a reescrever o DAL — controllers/BLL não mudam.
- **Reutilização**: a mesma classe `OrderService` é usada em endpoints web e poderia ser usada por uma futura API REST.
- **Separação de preocupações (Separation of Concerns)** — princípio fundamental do SOLID.
- **DRY**: a `BaseDAL` evita repetir o CRUD em todas as entidades.
