<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Cart;
use App\Entity\Stock;
use App\Entity\Store;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        //Fake table product
        $randomProduct = array();
        for ($i = 0; $i < 20; $i++) 
        {
            $product = new Product();
            $product->setName($faker->name);
            $product->setImage($faker->imageUrl(50, 60));
            $product->setDescription($faker->text(200));
            $product->setCategorie($faker->categorie);
            $product->setPrice($faker->price);

            $randomProduct [] = $product;
            $manager->persist($product);
        }
        $manager->flush();

        //Fake table  user

        $randomUser = array();
        for($i = 0; $i < 5; $i++ ) 
        {
            $User = new User();
            $User->setEmail($faker->unique()->email);
            $password = $this->encoder->encodePassword($User, 'password');
            $User->setPassword($password);
            $User->setFirstname($faker->firstName);
            $User->setLastname($faker->lastName);
            $User->setAddress($faker->address);
            $User->setRole(["ROLE_USER"]);

            $randomUser [] = $User; 
            $manager->persist($product);
        }
        $manager->flush();

        //Fake table Booking
        $randomBooking = array();
        for($i = 0; $i < 10; $i++ ) 
        {
            $booking = new Booking();
            $booking->setIsValidate($facker->boolean);
            $booking->setIsTerminated($facker->boolean);
            $booking->setUser($faker->randomElement($randomUser));

            $randomBooking [] = $booking;
            $manager->persist($booking);
        
        }
        $manager->flush();

        //Fake table Cart

        for($i = 0; $i < 2; $i++ ) 
        {
            $cart = new Cart();
            $cart->setQuantity($facker->quantity);
            $cart->setProduct($faker->randomElement($randomProduct));
            $cart->setBooking($faker->randomElement($randomBooking));

            $manager->persist($cart);
        }
        $manager->flush();
 
        //Fake table Stock
 
        for($i = 0; $i < 5; $i++ ) 
        {
            $stock = new Stock();
            $stock->setNbProduct($faker->randomDigitNotNull);
            $stock->setProduct($faker->randomElement($randomProduct));
            $stock->setStore($faker->randomElement($randomStore));

            $manager->persist($stock);          
        }
        $manager->flush();

        //Fake table Store
        $randomStore = array();
        for($i = 0; $i < 3; $i++ ) 
        {
            $store = new Store();
            $store->setName($faker->name);
            $store->setAddress($faker->address);

            $randomStore [] = $store; 
            $manager->persist($store);
        }
        $manager->flush();

    }
}