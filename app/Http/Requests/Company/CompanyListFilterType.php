<?php

namespace App\Http\Requests\Company;

enum CompanyListFilterType: string
{
    /** По названию */
    case NAME = 'name';
    /** По строению */
    case BUILDING = 'building';
    /** По направлению деятельности (включая дочерние) */
    case BUSINESS_DIRECTION = 'business_direction';
    /** По конкретному направлению деятельности (без дочерних) */
    case CONCRETE_BUSINESS_DIRECTION = 'concrete_business_direction';
    /** В радиусе от гео точки */
    case GEO_RADIUS = 'geo_radius';
    /** В прямоугольнике вокруг гео точки */
    case GEO_RECT = 'geo_rect';
}
