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
    static function calculateRectangleEdges(
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

    /**
     * @param $centerLat
     * @param $centerLon
     * @param $distanceKm
     * @return array{lat: float, lng: float}
     */
    static function generateRandomPointAtDistance($centerLat, $centerLon, $distanceKm): array
    {
        $earthRadius = 6371;

        // Случайный азимут (0-360 градусов)
        $bearing = deg2rad(mt_rand(0, 360));

        // Угловое расстояние в радианах
        $angularDistance = $distanceKm / $earthRadius;

        $centerLatRad = deg2rad($centerLat);
        $centerLonRad = deg2rad($centerLon);

        // Вычисляем новую точку
        $newLat = asin(sin($centerLatRad) * cos($angularDistance) +
            cos($centerLatRad) * sin($angularDistance) * cos($bearing));

        $newLon = $centerLonRad + atan2(sin($bearing) * sin($angularDistance) * cos($centerLatRad),
                cos($angularDistance) - sin($centerLatRad) * sin($newLat));

        // Нормализуем долготу
        $newLon = fmod(($newLon + 3 * M_PI), (2 * M_PI)) - M_PI;

        return [
            'lat' => rad2deg($newLat),
            'lng' => rad2deg($newLon),
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
