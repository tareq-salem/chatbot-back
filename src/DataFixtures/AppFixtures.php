<?php

namespace App\DataFixtures;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        //Faker table product
        $randomProduct = array();
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName($faker->name);
            $product->setImage($faker->imageUrl(50, 60));
            $product->setDescription($faker->description);
            $product->setCategorie($faker->categorie);
            $product->setPrice($faker->price);
    
            $randomProduct [] = $product;
            $manager->persist($product);
        }
        $manager->flush();

        //Fake table Booking
    }
}
