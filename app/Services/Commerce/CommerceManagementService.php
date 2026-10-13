<?php

namespace App\Services\Commerce;

use App\Grpc\Clients\CommerceClient;

class CommerceManagementService
{
    public function __construct(private CommerceClient $commerceClient) {}

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createCategory(array $input): array
    {
        return $this->commerceClient->createCategory($input);
    }

    /** @return array<string, mixed> */
    public function listCategories(int $pageSize = 25, string $pageToken = ''): array
    {
        return $this->commerceClient->listCategories($pageSize, $pageToken);
    }

    /** @return array<string, mixed> */
    public function getCategory(string $id): array
    {
        return $this->commerceClient->getCategory($id);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateCategory(array $input): array
    {
        return $this->commerceClient->updateCategory($input);
    }

    /** @return array<string, mixed> */
    public function deleteCategory(string $id): array
    {
        return $this->commerceClient->deleteCategory($id);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createProduct(array $input): array
    {
        return $this->commerceClient->createProduct($input);
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    public function listProducts(array $filters): array
    {
        return $this->commerceClient->listProducts($filters);
    }

    /** @return array<string, mixed> */
    public function getProduct(string $id): array
    {
        return $this->commerceClient->getProduct($id);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateProduct(array $input): array
    {
        return $this->commerceClient->updateProduct($input);
    }

    /** @return array<string, mixed> */
    public function deleteProduct(string $id): array
    {
        return $this->commerceClient->deleteProduct($id);
    }

    /** @return array<string, mixed> */
    public function getProductPage(string $id): array
    {
        return $this->commerceClient->getProductPage($id);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function saveProductDetails(array $input, bool $exists): array
    {
        return $this->commerceClient->saveProductDetails($input, $exists);
    }

    /** @return array<string, mixed> */
    public function deleteProductDetails(string $productId): array
    {
        return $this->commerceClient->deleteProductDetails($productId);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createProductImage(array $input): array
    {
        return $this->commerceClient->createProductImage($input);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateProductImage(array $input): array
    {
        return $this->commerceClient->updateProductImage($input);
    }

    /** @return array<string, mixed> */
    public function deleteProductImage(string $imageId): array
    {
        return $this->commerceClient->deleteProductImage($imageId);
    }

    /** @return array<string, mixed> */
    public function getProductImageData(string $imageId): array
    {
        return $this->commerceClient->getProductImageData($imageId);
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    public function listOrders(array $filters): array
    {
        return $this->commerceClient->listOrders($filters);
    }

    /** @return array<string, mixed> */
    public function getOrder(string $id): array
    {
        return $this->commerceClient->getOrder($id);
    }

    /** @return array<string, mixed> */
    public function updateOrderStatus(string $id, int $status, string $changedBy, string $note): array
    {
        return $this->commerceClient->updateOrderStatus($id, $status, $changedBy, $note);
    }
}
