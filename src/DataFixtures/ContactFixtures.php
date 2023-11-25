<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactFixtures extends Fixture
{
    private const NB_CONTACTS = 20;
    
    public function load(ObjectManager $manager): void
    {
        for ($i=0 ; $i < self::NB_CONTACTS; $i++) { 
            $entity = new Contact();
            $entity 
                -> setSubject("Contact $i")
                -> setEmail("Email $i")
                -> setMessage("Message  $i")
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
