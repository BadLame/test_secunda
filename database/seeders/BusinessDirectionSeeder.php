<?php

namespace Database\Seeders;

use App\Models\BusinessDirection;
use Illuminate\Database\Seeder;

class BusinessDirectionSeeder extends Seeder
{
    protected const array BUSINESS_DIRECTIONS = [
        [
            'title' => 'Еда',
            'children' => [
                ['title' => 'Мясная продукция'],
                ['title' => 'Молочная продукция'],
            ],
        ],
        [
            'title' => 'Автомобили',
            'children' => [
                ['title' => 'Грузовые'],
                [
                    'title' => 'Легковые',
                    'children' => [
                        ['title' => 'Запчасти'],
                        ['title' => 'Аксессуары'],
                    ],
                ],
            ],
        ],
    ];

    function run(): void
    {
        $this->saveFn(static::BUSINESS_DIRECTIONS);
    }

    protected function saveFn(array $items, ?BusinessDirection $parent = null): void
    {
        foreach ($items as $item) {
            /** @var BusinessDirection $mayBeParent */
            $mayBeParent = BusinessDirection::query()->updateOrCreate(
                ['title' => $item['title']],
                ['parent_id' => $parent?->id]
            );

            if (isset($item['children'])) {
                $this->saveFn($item['children'], $mayBeParent);
            }
        }
    }
}
