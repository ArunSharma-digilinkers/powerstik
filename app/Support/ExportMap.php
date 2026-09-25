<?php

namespace App\Support;

/**
 * The schematic export diagram: arcs fanning out from the Haryana hub over a
 * 620×420 viewBox. It is the fallback the home and global pages draw when the
 * client's own map artwork is not in place at public/media/home/export-map.webp.
 */
class ExportMap
{
    /**
     * The design's geometry crowded the first labels into the neighbouring arcs,
     * so the sweep here is wider and the arcs flatter.
     *
     * @param  list<string>  $names
     * @return list<array{d: string, x: float, y: float, name: string, anchor: string, lx: float, ly: float}>
     */
    public static function arcs(array $names): array
    {
        $hub = ['x' => 96, 'y' => 300];
        $n = count($names);

        return array_map(function (int $i) use ($names, $n, $hub) {
            $a = -M_PI * 0.4 + ($n > 1 ? $i / ($n - 1) : 0) * (M_PI * 0.44);
            $x = round($hub['x'] + cos($a) * 400 * 1.22, 1);
            $y = round($hub['y'] + sin($a) * 400 * 0.62, 1);
            $cx = round(($hub['x'] + $x) / 2, 1);
            $cy = round(min($hub['y'], $y) - 46, 1);

            return [
                'd' => "M{$hub['x']} {$hub['y']} Q{$cx} {$cy} {$x} {$y}",
                'x' => $x, 'y' => $y, 'name' => $names[$i],
                'anchor' => 'start',
                'lx' => $x + 10,
                'ly' => $y + 4,
            ];
        }, array_keys($names));
    }
}
