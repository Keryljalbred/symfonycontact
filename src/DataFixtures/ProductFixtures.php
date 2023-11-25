<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    private const NB_PRODUCTS = 20;
    
    public function load(ObjectManager $manager): void
    {
        for ($i=0 ; $i < self::NB_PRODUCTS; $i++) { 
            $entity = new Product();
            $entity 
                -> setName("Product $i")
                -> setPrice(mt_rand(1, 99))
                -> setDescription("Description Product $i")
                -> setQuantity(mt_rand(0, 10))
                -> setImage('image.jpg')
            ;
            $manager->persist($entity);

        }
        //creer un objet
        // $product = new Product();
        //pour creer une file d'attente(persister un objet)
        // $manager->persist($product);

        //executer la file d'attente
        $manager->flush();
    }
}
