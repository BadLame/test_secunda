<?php

namespace App\Http\Requests\Building;

/** Тип фильтрации списка объектов рядом с геоточкой */
enum BuildingListFilterType: string
{
    case RECT = 'rect';
    case RADIUS = 'radius';
}
