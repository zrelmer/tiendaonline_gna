<?php

namespace App\Support;

final class CategoriaIcon
{
    /** @var array<string, string> */
    private const SLUG_MAP = [
        'laptops' => 'ri-macbook-line',
        'celulares' => 'ri-smartphone-line',
        'accesorios' => 'ri-headphone-line',
        'tablets' => 'ri-tablet-line',
        'monitores' => 'ri-computer-line',
        'computadoras' => 'ri-computer-line',
        'audio' => 'ri-speaker-line',
        'gaming' => 'ri-gamepad-line',
        'redes' => 'ri-router-line',
        'impresoras' => 'ri-printer-line',
        'almacenamiento' => 'ri-hard-drive-2-line',
        'componentes' => 'ri-cpu-line',
        'camaras' => 'ri-camera-line',
        'smartwatch' => 'ri-time-line',
        'relojes' => 'ri-time-line',
        'televisores' => 'ri-tv-2-line',
        'tv' => 'ri-tv-2-line',
        'software' => 'ri-code-box-line',
        'cables' => 'ri-plug-line',
    ];

    /** @var array<string, string> */
    private const KEYWORD_MAP = [
        'laptop' => 'ri-macbook-line',
        'portatil' => 'ri-macbook-line',
        'portátil' => 'ri-macbook-line',
        'celular' => 'ri-smartphone-line',
        'telefono' => 'ri-smartphone-line',
        'teléfono' => 'ri-smartphone-line',
        'smartphone' => 'ri-smartphone-line',
        'accesorio' => 'ri-plug-line',
        'auricular' => 'ri-headphone-line',
        'audifono' => 'ri-headphone-line',
        'mouse' => 'ri-mouse-line',
        'teclado' => 'ri-keyboard-line',
        'tablet' => 'ri-tablet-line',
        'monitor' => 'ri-computer-line',
        'impresora' => 'ri-printer-line',
        'router' => 'ri-router-line',
        'wifi' => 'ri-wifi-line',
        'camara' => 'ri-camera-line',
        'cámara' => 'ri-camera-line',
        'consola' => 'ri-gamepad-line',
        'videojuego' => 'ri-gamepad-line',
        'disco' => 'ri-hard-drive-2-line',
        'memoria' => 'ri-sd-card-line',
        'cargador' => 'ri-battery-charge-line',
        'bateria' => 'ri-battery-2-line',
        'batería' => 'ri-battery-2-line',
    ];

    public static function remixClass(string $slug, ?string $nombre = null): string
    {
        $slugKey = strtolower(trim($slug));

        if (isset(self::SLUG_MAP[$slugKey])) {
            return self::SLUG_MAP[$slugKey];
        }

        $haystack = $slugKey.' '.strtolower((string) $nombre);

        foreach (self::KEYWORD_MAP as $keyword => $icon) {
            if (str_contains($haystack, $keyword)) {
                return $icon;
            }
        }

        return 'ri-price-tag-3-line';
    }
}
