<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ménage et nettoyage',
                'slug' => 'menage-nettoyage',
                'description' => 'Services de ménage, nettoyage de domicile et entretien.',
                'icon' => 'cleaning',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Garde d\'enfants',
                'slug' => 'garde-enfants',
                'description' => 'Services de garde d\'enfants et baby-sitting.',
                'icon' => 'baby',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Coiffure',
                'slug' => 'coiffure',
                'description' => 'Services de coiffure à domicile.',
                'icon' => 'scissors',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Beauté et soins',
                'slug' => 'beaute-soins',
                'description' => 'Services de beauté, esthétique et soins à domicile.',
                'icon' => 'sparkles',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Massage et bien-être',
                'slug' => 'massage-bien-etre',
                'description' => 'Services de massage et thérapies de bien-être.',
                'icon' => 'hand',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Coaching sportif',
                'slug' => 'coaching-sportif',
                'description' => 'Coaching sportif et cours de fitness à domicile.',
                'icon' => 'dumbbell',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Jardinage',
                'slug' => 'jardinage',
                'description' => 'Services de jardinage et entretien d\'espaces verts.',
                'icon' => 'leaf',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Bricolage',
                'slug' => 'bricolage',
                'description' => 'Services de bricolage et petits travaux.',
                'icon' => 'wrench',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categories created successfully!');
    }
}
