<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Orders;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class OrderServiceClient extends BaseStub
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
     * @param  CreateOrderRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderResponse>
     */
    public function CreateOrder(CreateOrderRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/CreateOrder',
            $argument,
            ['\Orders\OrderResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  GetOrderRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderResponse>
     */
    public function GetOrder(GetOrderRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/GetOrder',
            $argument,
            ['\Orders\OrderResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ListOrdersRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderList>
     */
    public function ListOrders(ListOrdersRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/ListOrders',
            $argument,
            ['\Orders\OrderList', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CancelOrderRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderResponse>
     */
    public function CancelOrder(CancelOrderRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/CancelOrder',
            $argument,
            ['\Orders\OrderResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  GetOrderAdminRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderResponse>
     */
    public function GetOrderAdmin(GetOrderAdminRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/GetOrderAdmin',
            $argument,
            ['\Orders\OrderResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ListOrdersAdminRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderList>
     */
    public function ListOrdersAdmin(ListOrdersAdminRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/ListOrdersAdmin',
            $argument,
            ['\Orders\OrderList', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  UpdateOrderStatusRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OrderResponse>
     */
    public function UpdateOrderStatus(UpdateOrderStatusRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/orders.OrderService/UpdateOrderStatus',
            $argument,
            ['\Orders\OrderResponse', 'decode'],
            $metadata, $options);
    }
}
