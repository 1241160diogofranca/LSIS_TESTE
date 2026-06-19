<?php
// BLL — Product Service
require_once __DIR__ . '/../dal/ProductDAL.php';
require_once __DIR__ . '/../dal/CategoryDAL.php';
require_once __DIR__ . '/../dal/PartDAL.php';

class ProductService {
    private ProductDAL $productDAL;
    private CategoryDAL $categoryDAL;
    private PartDAL $partDAL;

    public function __construct() {
        $this->productDAL  = new ProductDAL();
        $this->categoryDAL = new CategoryDAL();
        $this->partDAL     = new PartDAL();
    }

    public function homeData(): array {
        return [
            'categories' => $this->categoryDAL->allWithProductCount(),
            'featured'   => $this->productDAL->featured(4),
        ];
    }

    public function catalogData(?string $cat_slug, ?string $q, ?string $brand, ?float $maxPrice): array {
        return [
            'products'   => $this->productDAL->search($cat_slug, $q, $brand, $maxPrice),
            'categories' => $this->categoryDAL->findAll('id ASC'),
            'brands'     => $this->productDAL->distinctBrands(),
        ];
    }

    public function productPageData(int $id): ?array {
        $product = $this->productDAL->findByIdWithCategory($id);
        if (!$product) return null;
        return [
            'product' => $product,
            'parts'   => $this->partDAL->findCompatibleWith($product['model'], 6),
        ];
    }

    public function partsList(?string $q): array {
        return $this->partDAL->search($q);
    }

    public function adminProductsList(): array {
        return [
            'products'   => $this->productDAL->allWithCategoryName(),
            'categories' => $this->categoryDAL->findAll('name ASC'),
        ];
    }

    public function saveProduct(int $id, array $data): array {
        if (empty($data['sku']) || empty($data['name']) || empty($data['category_id'])) {
            return [false, 'SKU, nome e categoria são obrigatórios.'];
        }
        if ($id) {
            $this->productDAL->update($id, $data);
            return [true, 'Produto actualizado.'];
        }
        $this->productDAL->insert($data);
        return [true, 'Produto criado.'];
    }
}
