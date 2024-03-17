<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn (string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name))), [
            "Oignon",
            "Ail",
            "Tomate",
            "Poivron",
            "Pomme de terre",
            "Carotte",
            "Céleri",
            "Champignon",
            "Courgette",
            "Aubergine",
            "Poulet",
            "Boeuf",
            "Poisson",
            "Crevettes",
            "Pâtes",
            "Riz",
            "Huile d'olive",
            "Vinaigre balsamique",
            "Sel",
            "Poivre"
        ]);

        $units = [
            "c. à s.",
            "c. à c.",
            "tasse",
            "verre",
            "g",
            "kg",
            "L",
            "ml",
            "oz",
            "pincée"
        ];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categoriesName = ['Plat chaud', 'Dessert', 'Entrée', 'Goûter'];
        foreach ($categoriesName as $categoryName) {
            $category = new Category();
            $category->setName($categoryName)
                ->setSlug(strtolower($this->slugger->slug($category->getName())))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            $this->addReference($category->getName(), $category);
        }

        for ($i = 0; $i < 10; $i++) {
            $recipe = new Recipe();
            $recipe->setTitle($faker->foodName())
                ->setSlug(strtolower($this->slugger->slug($recipe->getTitle())))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                ->setDuration($faker->numberBetween(2, 60))
                ->setCategory($this->getReference($faker->randomElement($categoriesName)))
                ->setUser($this->getReference('USER' . $faker->randomNumber(1, 10)));

            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1, 250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient));
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
