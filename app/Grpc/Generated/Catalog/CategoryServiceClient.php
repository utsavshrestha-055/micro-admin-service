<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Catalog;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class CategoryServiceClient extends BaseStub
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
     * @param  CategoryInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<CategoryResponse>
     */
    public function CreateCategory(CategoryInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.CategoryService/CreateCategory',
            $argument,
            ['\Catalog\CategoryResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CategoryId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<CategoryResponse>
     */
    public function GetCategory(CategoryId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.CategoryService/GetCategory',
            $argument,
            ['\Catalog\CategoryResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  PageRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<CategoryList>
     */
    public function ListCategories(PageRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.CategoryService/ListCategories',
            $argument,
            ['\Catalog\CategoryList', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CategoryInput  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<CategoryResponse>
     */
    public function UpdateCategory(CategoryInput $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.CategoryService/UpdateCategory',
            $argument,
            ['\Catalog\CategoryResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CategoryId  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteCategory(CategoryId $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/catalog.CategoryService/DeleteCategory',
            $argument,
            ['\Catalog\OperationResponse', 'decode'],
            $metadata, $options);
    }
}
