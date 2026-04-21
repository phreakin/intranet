<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$testFiles = array_values(array_filter(
    scandir(__DIR__),
    static fn (string $file): bool => str_ends_with($file, 'Test.php')
));

sort($testFiles);

$failures = [];
$executed = 0;

foreach ($testFiles as $file) {
    require_once __DIR__ . '/' . $file;
}

foreach (get_declared_classes() as $className) {
    if (!str_starts_with($className, 'Tests\\')) {
        continue;
    }

    if (!is_subclass_of($className, \Tests\TestCase::class)) {
        continue;
    }

    $reflection = new ReflectionClass($className);
    $instance = $reflection->newInstance();

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->getDeclaringClass()->getName() !== $className) {
            continue;
        }

        if (!str_starts_with($method->getName(), 'test')) {
            continue;
        }

        $executed++;
        $label = $className . '::' . $method->getName();

        try {
            $instance->run($method->getName());
            echo "[PASS] $label\n";
        } catch (Throwable $throwable) {
            $failures[] = [
                'label' => $label,
                'message' => $throwable->getMessage(),
            ];
            echo "[FAIL] $label\n";
        }
    }
}

echo "\nExecuted $executed test(s).\n";

if ($failures !== []) {
    echo count($failures) . " failure(s):\n";
    foreach ($failures as $failure) {
        echo "- {$failure['label']}\n";
        echo "  {$failure['message']}\n";
    }

    exit(1);
}

echo "All tests passed.\n";

