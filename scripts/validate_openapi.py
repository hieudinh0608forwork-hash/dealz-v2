#!/usr/bin/env python3
"""Validate the DealZ OpenAPI document and its internal references."""

from pathlib import Path
import re
import sys

import yaml


SPEC_PATH = Path(__file__).resolve().parents[1] / "docs" / "openapi" / "dealz-v1.openapi.yaml"


def fail(message: str) -> None:
    raise SystemExit(f"OpenAPI validation failed: {message}")


def resolve_pointer(document, pointer: str):
    if not pointer.startswith("#/"):
        fail(f"External or unsupported reference: {pointer}")
    value = document
    for token in pointer[2:].split("/"):
        token = token.replace("~1", "/").replace("~0", "~")
        if not isinstance(value, dict) or token not in value:
            fail(f"Unresolved reference: {pointer}")
        value = value[token]
    return value


def walk(value, document) -> None:
    if isinstance(value, dict):
        reference = value.get("$ref")
        if reference:
            resolve_pointer(document, reference)
        for child in value.values():
            walk(child, document)
    elif isinstance(value, list):
        for child in value:
            walk(child, document)


def main() -> None:
    try:
        document = yaml.safe_load(SPEC_PATH.read_text(encoding="utf-8"))
    except Exception as error:
        fail(f"YAML parsing error: {error}")

    if not isinstance(document, dict) or document.get("openapi") != "3.1.0":
        fail("document must declare OpenAPI 3.1.0")

    paths = document.get("paths")
    if not isinstance(paths, dict) or not paths:
        fail("paths must be a non-empty mapping")

    walk(document, document)

    operation_ids = []
    for route, path_item in paths.items():
        if not isinstance(path_item, dict):
            fail(f"path item is not a mapping: {route}")

        placeholders = set(re.findall(r"\{([^}]+)\}", route))
        path_parameters = list(path_item.get("parameters", []))

        for method, operation in path_item.items():
            if method.lower() not in {"get", "post", "put", "patch", "delete", "options", "head", "trace"}:
                continue
            if not isinstance(operation, dict):
                fail(f"operation is not a mapping: {method.upper()} {route}")

            operation_id = operation.get("operationId")
            if operation_id:
                operation_ids.append(operation_id)

            declared = path_parameters + list(operation.get("parameters", []))
            declared_path_names = {
                parameter.get("name")
                for parameter in declared
                if isinstance(parameter, dict) and parameter.get("in") == "path"
            }
            missing = placeholders - declared_path_names
            if missing:
                fail(f"{method.upper()} {route} misses path parameters: {', '.join(sorted(missing))}")

    if len(operation_ids) != len(set(operation_ids)):
        fail("operationId values must be unique")

    print(
        f"OpenAPI validation passed: {len(paths)} paths, "
        f"{len(operation_ids)} operations, all internal references resolved."
    )


if __name__ == "__main__":
    main()
