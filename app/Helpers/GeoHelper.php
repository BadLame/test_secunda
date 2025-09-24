<?php

namespace App\Helpers;

class GeoHelper
{
    const int EARTH_RADIUS = 6_371_000; // Радиус Земли в метрах

    /**
     * Вычисляет координаты углов прямоугольника по центру и размерам
     *
     * @param float $centerLat Широта центра в градусах
     * @param float $centerLng Долгота центра в градусах
     * @param float $widthMeters Ширина в метрах
     * @param float $heightMeters Высота в метрах
     * @return array{n: float, s: float, w: float, e: float} Координаты севера, юга, запада, востока
     */
    static function calculateRectangleCorners(
        float $centerLat,
        float $centerLng,
        float $widthMeters,
        float $heightMeters
    ): array
    {
        // Преобразуем метры в градусы
        $latDelta = static::metersToLatitudeDegrees($heightMeters / 2);
        $lngDelta = static::metersToLongitudeDegrees($widthMeters / 2, $centerLat);

        return [
            'n' => $centerLat + $latDelta,
            's' => $centerLat - $latDelta,
            'w' => $centerLng - $lngDelta,
            'e' => $centerLng + $lngDelta,
        ];
    }

    /** Преобразует метры в градусы широты */
    static function metersToLatitudeDegrees(float $meters): float
    {
        return rad2deg($meters / self::EARTH_RADIUS);
    }

    /** Преобразует метры в градусы долготы (зависит от широты) */
    static function metersToLongitudeDegrees(float $meters, float $latitude): float
    {
        return rad2deg($meters / (self::EARTH_RADIUS * cos(deg2rad($latitude))));
    }
}
