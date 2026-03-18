<?php

declare(strict_types=1);

namespace TypeHintDeprecatedInClosureSignature;

$a = function (
    DeprecatedProperty $property,
    ?DeprecatedInterface $property2,
    $property3,
    VerboseDeprecatedProperty $property4,
    string $property5
): ?DeprecatedProperty {
    return $property;
};
