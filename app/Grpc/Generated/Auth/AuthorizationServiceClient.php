<?php

// GENERATED CODE -- DO NOT EDIT!

namespace Auth;

use Grpc\BaseStub;
use Grpc\Channel;
use Grpc\UnaryCall;

class AuthorizationServiceClient extends BaseStub
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
     * @param  AssignRoleRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function AssignRole(AssignRoleRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/AssignRole',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  UnassignRoleRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function UnassignRole(UnassignRoleRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/UnassignRole',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CheckPermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<PermissionResponse>
     */
    public function CheckPermission(CheckPermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/CheckPermission',
            $argument,
            ['\Auth\PermissionResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CreateRoleRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<RoleResponse>
     */
    public function CreateRole(CreateRoleRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/CreateRole',
            $argument,
            ['\Auth\RoleResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  AccessTokenRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<RoleListResponse>
     */
    public function ListRoles(AccessTokenRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/ListRoles',
            $argument,
            ['\Auth\RoleListResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  UpdateRoleRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<RoleResponse>
     */
    public function UpdateRole(UpdateRoleRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/UpdateRole',
            $argument,
            ['\Auth\RoleResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  DeleteRoleRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeleteRole(DeleteRoleRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/DeleteRole',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  CreatePermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<PermissionInfoResponse>
     */
    public function CreatePermission(CreatePermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/CreatePermission',
            $argument,
            ['\Auth\PermissionInfoResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  AccessTokenRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<PermissionListResponse>
     */
    public function ListPermissions(AccessTokenRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/ListPermissions',
            $argument,
            ['\Auth\PermissionListResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  UpdatePermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<PermissionInfoResponse>
     */
    public function UpdatePermission(UpdatePermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/UpdatePermission',
            $argument,
            ['\Auth\PermissionInfoResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  DeletePermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function DeletePermission(DeletePermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/DeletePermission',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  RolePermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function GrantPermissionToRole(RolePermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/GrantPermissionToRole',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  RolePermissionRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<OperationResponse>
     */
    public function RevokePermissionFromRole(RolePermissionRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/RevokePermissionFromRole',
            $argument,
            ['\Auth\OperationResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ListRolePermissionsRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<PermissionListResponse>
     */
    public function ListRolePermissions(ListRolePermissionsRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/ListRolePermissions',
            $argument,
            ['\Auth\PermissionListResponse', 'decode'],
            $metadata, $options);
    }

    /**
     * @param  ListUserRolesRequest  $argument  input argument
     * @param  array  $metadata  metadata
     * @param  array  $options  call options
     * @return UnaryCall<StringListResponse>
     */
    public function ListUserRoles(ListUserRolesRequest $argument,
        $metadata = [], $options = [])
    {
        return $this->_simpleRequest('/auth.AuthorizationService/ListUserRoles',
            $argument,
            ['\Auth\StringListResponse', 'decode'],
            $metadata, $options);
    }
}
