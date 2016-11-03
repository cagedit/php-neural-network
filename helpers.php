<?php
use Layers\Layer;

function dd($val): void
{
    var_dump($val);
    die;
}

function array_get(array $array, string $key, $default = '')
{
    $key = explode('.', $key);

    $value = $array;

    foreach ($key as $arrayKey) {
        $value = $value[$arrayKey] ?? $default;
    }

    return $value;
}

function array_first(array $array)
{
    reset($array);

    return current($array);
}

function randFloat()
{
    return rand(0, 1000) / 1000;
}

function sigmoid($t)
{
    return 1 / (1 + pow(M_E, - $t));
}

function sigmoidPrime($t)
{
    return sigmoid($t) * (1 - sigmoid($t));
}

function compressArray(array $array)
{
    $compressed = [];
    foreach ($array as $items) {
        foreach ($items as $id => $item) {
            $compressed[] = $item;
        }
    }

    return $compressed;
}

function transferFnc(float $t)
{
    return tanh($t);
}

function transferDerivativeFnc(float $t)
{
    return (1.0 - $t * $t);
}

function println(string $msg, int $indent = 0)
{
    if ($indent) {
        foreach (range(0, $indent) as $i) {
            echo "\t";
        }
    }
    echo $msg . PHP_EOL;
}

function showVectorVals($label,array $v)
{
    echo "$label ".implode(" ",$v)."\n";
}