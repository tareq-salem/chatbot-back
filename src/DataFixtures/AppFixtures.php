<?php

namespace App\DataFixtures;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Cart;
use App\Entity\Stock;
use App\Entity\Store;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture

{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        

            $admin = new User();
            $admin->setEmail('admin@admin.fr');
            $admin->setPassword($this->encoder->encodePassword($admin, 'root'));
            $admin->setFirstname("Faresse");
            $admin->setLastname("TAHLAITI");
            $admin->setAddress("5 rue du gay paul 6900");
            $admin->setRoles(["ROLE_ADMIN"]);
            $admin->setApiToken('gggggggggg');

            $manager->persist($admin);
            $manager->flush();

        
        $faker = Factory::create('fr_FR');

        //Fake table product
        $randomProduct = array();
        $randomCategorie = ['Livre','Jouet','Figurine','Vêtement'];
        for ($i = 0; $i < 20; $i++)
        {
            $product = new Product();
            $product->setName($faker->name);
            $product->setImage($faker->imageUrl(50, 60));
            $product->setDescription($faker->text(200));
            $product->setCategorie($faker->randomElement($randomCategorie));
            $product->setPrice($faker->numerify("## €"));

            $randomProduct [] = $product;
            $manager->persist($product);
        }
        $manager->flush();

        //Fake table  user
        $randomUser = array();
        $randomRoles =['ROLE_USER'];
        for($i = 0; $i < 5; $i++ )
        {
            $User = new User();
            $User->setEmail($faker->unique()->email);
            $password = $this->encoder->encodePassword($User,'password');
            $User->setPassword($password);
            $User->setFirstname($faker->firstName);
            $User->setLastname($faker->lastName);
            $User->setAddress($faker->address);
            $User->setApiToken($faker->text(10));

            $User->setRoles([$faker->randomElement($randomRoles)]);

            $randomUser [] = $User; 
            $manager->persist($User);
        }
        $manager->flush();

        //Fake table Booking
        $randomBooking = array();
        for($i = 0; $i < 10; $i++ ) 
        {
            $booking = new Booking();
            $booking->setIsValidate($faker->boolean);
            $booking->setIsTerminated($faker->boolean);
            $booking->setUser($faker->randomElement($randomUser));

            $randomBooking [] = $booking;
            $manager->persist($booking);
        
        }
        $manager->flush();

        //Fake table Cart
        for($i = 0; $i < 2; $i++ ) 
        {
            $cart = new Cart();
            $cart->setQuantity($faker->numerify("##"));
            $cart->setProduct($faker->randomElement($randomProduct));
            $cart->setBooking($faker->randomElement($randomBooking));

            $manager->persist($cart);
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
    }
  
}