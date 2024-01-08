<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function create(string $id, string $name): Product
    {
        $product = new Product($id, $name);

        $this->productRepository->save($product);

        return $product;
    }

    /**
     * @return array<int, Product>
     */
    public function all(): array
    {
        return $this->productRepository->all();
    }
}
