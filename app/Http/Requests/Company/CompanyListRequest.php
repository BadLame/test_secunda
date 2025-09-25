<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Company\CompanyListFilterType as FilterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string<FilterType> $filter_type
 * @property string|null $name
 * @property string|null $building
 * @property string|null $bd_code
 * @property float|null $point_lat
 * @property float|null $point_lng
 * @property float|null $radius
 * @property float|null $distance_for_lat
 * @property float|null $distance_for_lng
 */
class CompanyListRequest extends FormRequest
{
    public ?FilterType $filterType;

    function rules(): array
    {
        $nameRequired = Rule::requiredIf(fn () => $this->filter_type == FilterType::NAME->value);
        $buildingRequired = Rule::requiredIf(fn () => $this->filter_type == FilterType::BUILDING->value);
        $dirRequired = Rule::requiredIf(fn () => in_array($this->filter_type, [
            FilterType::BUSINESS_DIRECTION->value,
            FilterType::CONCRETE_BUSINESS_DIRECTION,
        ]));
        $pointRequired = Rule::requiredIf(fn () => in_array($this->filter_type, [
            FilterType::GEO_RADIUS->value,
            FilterType::GEO_RECT->value,
        ]));
        $radiusRequired = Rule::requiredIf(fn () => $this->filter_type == FilterType::GEO_RADIUS->value);
        $rectsRequired = Rule::requiredIf(fn () => $this->filter_type == FilterType::GEO_RECT->value);

        return [
            'filter_type' => ['required', Rule::enum(FilterType::class)],
            // Наименование компании, **filter_type** = **name**
            'name' => [$nameRequired, 'string'],
            // ID строения, **filter_type** = **building**
            'building' => [$buildingRequired, 'uuid', Rule::exists('buildings', 'id')],
            // **code** направления деятельности, **filter_type** = **business_direction** / **concrete_business_direction**
            'bd_code' => [
                $dirRequired,
                'string',
                Rule::exists('business_directions', 'code'),
            ],
            // Широта гео точки, **filter_type** = **geo_radius** / **geo_rect**
            'point_lat' => [$pointRequired, 'numeric', 'between:-90,90'],
            // Долгота гео точки, **filter_type** = **geo_radius** / **geo_rect**
            'point_lng' => [$pointRequired, 'numeric', 'between:-180,180'],
            // Радиус в километрах, **filter_type** = **geo_radius**
            'radius' => [$radiusRequired, 'numeric'],
            // Дистанция по 'вертикали'/широте для прямоугольника, **filter_type** = **geo_rect**
            'distance_for_lat' => [$rectsRequired, 'numeric'],
            // Дистанция по 'горизонтали'/долготе для прямоугольника, **filter_type** = **geo_rect**
            'distance_for_lng' => [$rectsRequired, 'numeric'],
        ];
    }

    function after(): array
    {
        return [
            fn () => $this->filterType = FilterType::tryFrom($this->filter_type),
        ];
    }
}
