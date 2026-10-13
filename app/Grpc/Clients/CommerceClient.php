<?php

namespace App\Grpc\Clients;

use App\Grpc\GrpcTransport;

class CommerceClient
{
    public function __construct(private GrpcTransport $transport) {}

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createCategory(array $input): array
    {
        return $this->transport->call('CategoryService', 'CreateCategory', $input);
    }

    /** @return array<string, mixed> */
    public function listCategories(int $pageSize = 25, string $pageToken = ''): array
    {
        return $this->transport->call('CategoryService', 'ListCategories', ['pageSize' => $pageSize, 'pageToken' => $pageToken]);
    }

    /** @return array<string, mixed> */
    public function getCategory(string $id): array
    {
        return $this->transport->call('CategoryService', 'GetCategory', ['id' => $id]);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateCategory(array $input): array
    {
        return $this->transport->call('CategoryService', 'UpdateCategory', $input);
    }

    /** @return array<string, mixed> */
    public function deleteCategory(string $id): array
    {
        return $this->transport->call('CategoryService', 'DeleteCategory', ['id' => $id]);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createProduct(array $input): array
    {
        return $this->transport->call('ProductService', 'CreateProduct', $input);
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    public function listProducts(array $filters): array
    {
        return $this->transport->call('ProductService', 'ListProducts', $filters);
    }

    /** @return array<string, mixed> */
    public function getProduct(string $id): array
    {
        return $this->transport->call('ProductService', 'GetProduct', ['id' => $id]);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateProduct(array $input): array
    {
        return $this->transport->call('ProductService', 'UpdateProduct', $input);
    }

    /** @return array<string, mixed> */
    public function deleteProduct(string $id): array
    {
        return $this->transport->call('ProductService', 'DeleteProduct', ['id' => $id]);
    }

    /** @return array<string, mixed> */
    public function getProductPage(string $id): array
    {
        return $this->transport->call('ProductService', 'GetProductPage', ['id' => $id]);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function saveProductDetails(array $input, bool $exists): array
    {
        return $this->transport->call('ProductService', $exists ? 'UpdateProductDetails' : 'CreateProductDetails', $input);
    }

    /** @return array<string, mixed> */
    public function deleteProductDetails(string $productId): array
    {
        return $this->transport->call('ProductService', 'DeleteProductDetails', ['id' => $productId]);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function createProductImage(array $input): array
    {
        return $this->transport->call('ProductImageService', 'CreateProductImage', $input);
    }

    /** @param array<string, mixed> $input @return array<string, mixed> */
    public function updateProductImage(array $input): array
    {
        return $this->transport->call('ProductImageService', 'UpdateProductImage', $input);
    }

    /** @return array<string, mixed> */
    public function deleteProductImage(string $id): array
    {
        return $this->transport->call('ProductImageService', 'DeleteProductImage', ['id' => $id]);
    }

    /** @return array<string, mixed> */
    public function getProductImageData(string $id): array
    {
        return $this->transport->call('ProductImageService', 'GetProductImageData', ['id' => $id]);
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    public function listOrders(array $filters): array
    {
        return $this->transport->call('OrderService', 'ListOrdersAdmin', $filters);
    }

    /** @return array<string, mixed> */
    public function getOrder(string $id): array
    {
        return $this->transport->call('OrderService', 'GetOrderAdmin', ['id' => $id]);
    }

    /** @return array<string, mixed> */
    public function updateOrderStatus(string $id, int $status, string $changedBy, string $note): array
    {
        return $this->transport->call('OrderService', 'UpdateOrderStatus', [
            'id' => $id,
            'status' => $status,
            'changedBy' => $changedBy,
            'note' => $note,
        ]);
    }
}
