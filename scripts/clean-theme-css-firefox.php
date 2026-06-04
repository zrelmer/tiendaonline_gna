<?php

/**
 * Elimina reglas CSS con selectores legacy que Firefox marca como inválidos.
 *
 * Uso: php scripts/clean-theme-css-firefox.php [ruta/al/archivo.css]
 */
$path = $argv[1] ?? dirname(__DIR__).'/public/assets/css/style.css';

$removeIfSelectorContains = [
    ':-ms-input-placeholder',
    '::-ms-input-placeholder',
    ':-moz-placeholder-shown',
    '::-moz-focus-inner',
    '::-moz-focus-outer',
    '::-webkit-slider-thumb',
    '::-webkit-slider-runnable-track',
    '::-webkit-input-placeholder',
];

$css = file_get_contents($path);
$out = '';
$len = strlen($css);
$i = 0;

while ($i < $len) {
    if ($css[$i] !== '{') {
        $out .= $css[$i];
        $i++;

        continue;
    }

    $selectorStart = strrpos($out, '}');
    $selectorStart = $selectorStart === false ? 0 : $selectorStart + 1;
    $selector = substr($out, $selectorStart);

    $depth = 0;
    $j = $i;
    while ($j < $len) {
        if ($css[$j] === '{') {
            $depth++;
        } elseif ($css[$j] === '}') {
            $depth--;
            if ($depth === 0) {
                $j++;
                break;
            }
        }
        $j++;
    }

    $block = substr($css, $i, $j - $i);
    $skip = false;
    foreach ($removeIfSelectorContains as $needle) {
        if (str_contains($selector, $needle)) {
            $skip = true;
            break;
        }
    }

    if (! $skip) {
        $out .= $block;
    } else {
        $out = rtrim(substr($out, 0, $selectorStart));
    }

    $i = $j;
}

file_put_contents($path, $out);
echo "Limpieza aplicada en {$path}\n";
