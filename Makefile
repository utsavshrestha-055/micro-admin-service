PROTOC_BINARY ?= protoc
GRPC_PHP_PLUGIN_BINARY ?= $(shell command -v grpc_php_plugin)
PROTO_FILE ?= $(if $(AUTH_GRPC_PROTO_FILE),$(AUTH_GRPC_PROTO_FILE),proto/auth.proto)
PROTO_ROOT ?= $(patsubst %/,%,$(dir $(PROTO_FILE)))
PROTO_NAME ?= $(notdir $(PROTO_FILE))
GRPC_PHP_OUT ?= app/Grpc/Generated

.PHONY: grpc-stubs
grpc-stubs:
	mkdir -p "$(GRPC_PHP_OUT)"
	"$(PROTOC_BINARY)" --proto_path="$(PROTO_ROOT)" --php_out="$(GRPC_PHP_OUT)" --grpc_out="$(GRPC_PHP_OUT)" --plugin=protoc-gen-grpc="$(GRPC_PHP_PLUGIN_BINARY)" "$(PROTO_NAME)"
