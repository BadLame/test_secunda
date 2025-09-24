<?php

namespace App\Http\Requests\Building;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property float $point_lat
 * @property float $point_lng
 * @property string<BuildingListFilterType> $filter_type
 * @property float|null $radius
 * @property float|null $distance_for_lat
 * @property float|null $distance_for_lng
 */
class BuildingsListRequest extends FormRequest
{
    public ?BuildingListFilterType $filterType;

    function rules(): array
    {
        $radiusRequiredRule = Rule::requiredIf(
            fn () => $this->filter_type == BuildingListFilterType::RADIUS->value
        );
        $rectDistanceRequiredRule = Rule::requiredIf(
            fn () => $this->filter_type == BuildingListFilterType::RECT->value
        );

        return [
            'point_lat' => 'required|numeric|between:-90,90',
            'point_lng' => 'required|numeric|between:-180,180',
            // Тип поиска - радиус или прямоугольник
            'filter_type' => ['required', Rule::enum(BuildingListFilterType::class)],
            // Радиус вокруг точки в километрах. Обязателен для типа **radius**
            'radius' => [$radiusRequiredRule, 'numeric'],
            // Дистанция по 'вертикали'/широте для прямоугольника. Обязательна для типа **rect**
            'distance_for_lat' => [$rectDistanceRequiredRule, 'numeric'],
            // Дистанция по 'горизонтали'/долготе для прямоугольника. Обязательна для типа **rect**
            'distance_for_lng' => [$rectDistanceRequiredRule, 'numeric'],
        ];
    }

    function after(): array
    {
        return [
            fn () => $this->filterType = BuildingListFilterType::tryFrom($this->filter_type),
        ];
    }
}
