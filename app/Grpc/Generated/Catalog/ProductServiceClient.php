<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Catalog;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class ProductServiceClient extends BaseStub
{
    /**
     * @param  string  $hostname  hostname
     * @param  array  $opts  channel options
     * @param  Channel  $channel  (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param  ProductInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductResponse>
     */
    public function CreateProduct(ProductInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/CreateProduct',
            $argument,
            ['\Catalog\ProductResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductResponse>
     */
    public function GetProduct(ProductId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/GetProduct',
            $argument,
            ['\Catalog\ProductResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductListRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductList>
     */
    public function ListProducts(ProductListRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/ListProducts',
            $argument,
            ['\Catalog\ProductList', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductResponse>
     */
    public function UpdateProduct(ProductInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/UpdateProduct',
            $argument,
            ['\Catalog\ProductResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteProduct(ProductId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/DeleteProduct',
            $argument,
            ['\Catalog\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductDetailsInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductDetailsResponse>
     */
    public function CreateProductDetails(ProductDetailsInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/CreateProductDetails',
            $argument,
            ['\Catalog\ProductDetailsResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductDetailsResponse>
     */
    public function GetProductDetails(ProductId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/GetProductDetails',
            $argument,
            ['\Catalog\ProductDetailsResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductDetailsInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductDetailsResponse>
     */
    public function UpdateProductDetails(ProductDetailsInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/UpdateProductDetails',
            $argument,
            ['\Catalog\ProductDetailsResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteProductDetails(ProductId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/DeleteProductDetails',
            $argument,
            ['\Catalog\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductPageResponse>
     */
    public function GetProductPage(ProductId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductService/GetProductPage',
            $argument,
            ['\Catalog\ProductPageResponse', 'decode'],
            $metadata, $options);
    }
}
