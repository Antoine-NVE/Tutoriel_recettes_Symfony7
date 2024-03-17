<?php

namespace App\DataFixtures;

use App\Entity\Category;
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
            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
