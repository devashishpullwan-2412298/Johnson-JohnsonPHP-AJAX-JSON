<?php

function json_schema_validate(mixed $data, array $schema, string $path = '$'): array
{
    $errors = [];
    $types = $schema['type'] ?? null;
    $allowedTypes = is_array($types) ? $types : ($types !== null ? [$types] : []);

    if ($allowedTypes !== [] && !json_value_matches_types($data, $allowedTypes)) {
        $errors[] = sprintf(
            '%s should be of type %s.',
            $path,
            implode('|', $allowedTypes)
        );
        return $errors;
    }

    if (($schema['type'] ?? null) === 'object' || in_array('object', $allowedTypes, true)) {
        if (!is_array($data) || array_is_list($data)) {
            return $errors;
        }

        foreach (($schema['required'] ?? []) as $requiredKey) {
            if (!array_key_exists($requiredKey, $data)) {
                $errors[] = sprintf('%s.%s is required.', $path, $requiredKey);
            }
        }

        foreach (($schema['properties'] ?? []) as $property => $propertySchema) {
            if (array_key_exists($property, $data)) {
                $errors = array_merge(
                    $errors,
                    json_schema_validate($data[$property], $propertySchema, $path . '.' . $property)
                );
            }
        }
    }

    if (($schema['type'] ?? null) === 'array' || in_array('array', $allowedTypes, true)) {
        if (!is_array($data) || !array_is_list($data)) {
            return $errors;
        }

        foreach ($data as $index => $item) {
            $errors = array_merge(
                $errors,
                json_schema_validate($item, $schema['items'] ?? [], $path . '[' . $index . ']')
            );
        }
    }

    if (is_string($data)) {
        if (isset($schema['minLength']) && mb_strlen($data) < (int) $schema['minLength']) {
            $errors[] = sprintf('%s must have at least %d characters.', $path, (int) $schema['minLength']);
        }

        if (($schema['format'] ?? null) === 'date-time' && strtotime($data) === false) {
            $errors[] = sprintf('%s must be a valid date-time string.', $path);
        }
    }

    if ((is_int($data) || is_float($data)) && isset($schema['minimum']) && $data < $schema['minimum']) {
        $errors[] = sprintf('%s must be greater than or equal to %s.', $path, (string) $schema['minimum']);
    }

    if (isset($schema['enum']) && !in_array($data, $schema['enum'], true)) {
        $errors[] = sprintf('%s must be one of: %s.', $path, implode(', ', $schema['enum']));
    }

    return $errors;
}

function json_value_matches_types(mixed $value, array $allowedTypes): bool
{
    foreach ($allowedTypes as $type) {
        if ($type === 'null' && $value === null) {
            return true;
        }

        if ($type === 'string' && is_string($value)) {
            return true;
        }

        if ($type === 'integer' && is_int($value)) {
            return true;
        }

        if ($type === 'number' && (is_int($value) || is_float($value))) {
            return true;
        }

        if ($type === 'boolean' && is_bool($value)) {
            return true;
        }

        if ($type === 'array' && is_array($value) && array_is_list($value)) {
            return true;
        }

        if ($type === 'object' && is_array($value) && !array_is_list($value)) {
            return true;
        }
    }

    return false;
}
