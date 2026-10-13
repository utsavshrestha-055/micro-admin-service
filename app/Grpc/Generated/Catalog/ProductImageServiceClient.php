<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Catalog;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class ProductImageServiceClient extends BaseStub
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
     * @param  ProductImageInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductImageResponse>
     */
    public function CreateProductImage(ProductImageInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/CreateProductImage',
            $argument,
            ['\Catalog\ProductImageResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ImageId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductImageResponse>
     */
    public function GetProductImage(ImageId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/GetProductImage',
            $argument,
            ['\Catalog\ProductImageResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductImageListRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductImageList>
     */
    public function ListProductImages(ProductImageListRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/ListProductImages',
            $argument,
            ['\Catalog\ProductImageList', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ProductImageInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductImageResponse>
     */
    public function UpdateProductImage(ProductImageInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/UpdateProductImage',
            $argument,
            ['\Catalog\ProductImageResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ImageId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteProductImage(ImageId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/DeleteProductImage',
            $argument,
            ['\Catalog\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ImageId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<ProductImageDataResponse>
     */
    public function GetProductImageData(ImageId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.ProductImageService/GetProductImageData',
            $argument,
            ['\Catalog\ProductImageDataResponse', 'decode'],
            $metadata, $options);
    }
}
