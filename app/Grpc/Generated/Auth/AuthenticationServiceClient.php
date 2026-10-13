<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Auth;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class AuthenticationServiceClient extends BaseStub
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
     * @param  HealthCheckRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<HealthCheckResponse>
     */
    public function HealthCheck(HealthCheckRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/HealthCheck',
            $argument,
            ['\Auth\HealthCheckResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  RegisterRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<AuthResponse>
     */
    public function Register(RegisterRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/Register',
            $argument,
            ['\Auth\AuthResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  LoginRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<AuthResponse>
     */
    public function Login(LoginRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/Login',
            $argument,
            ['\Auth\AuthResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  GetUserRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<UserResponse>
     */
    public function GetUser(GetUserRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/GetUser',
            $argument,
            ['\Auth\UserResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ListUsersRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<UserListResponse>
     */
    public function ListUsers(ListUsersRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/ListUsers',
            $argument,
            ['\Auth\UserListResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  UpdateUserRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<UserResponse>
     */
    public function UpdateUser(UpdateUserRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/UpdateUser',
            $argument,
            ['\Auth\UserResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  DeleteUserRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteUser(DeleteUserRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/DeleteUser',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  AccessTokenRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function Logout(AccessTokenRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthenticationService/Logout',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }
}
