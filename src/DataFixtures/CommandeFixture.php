<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
// use Faker\Factory;
use Faker\Provider\Base;


/* Les Fixtures sont des class servants à injecter des données en BDD grâce à une ligne de commande.
* C'est un "jeu de données" complétement FACTICE.
* Cela vous permet d'avoir des données en BDD sans avoir de CRUD sur vos entités (c-a-d pas de formulaire de création)
* Cela vous sert au tout début du développement d'un projet, pour avoir des données à manipulées en front et en back.
*/

class CommandeFixture extends Fixture
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * Cette fonction load() sera exécutée par la ligne de commande php bin/console doctrine:fixtures:load --append
     * Elle ne peut pas prendre d'autres dépendances en injection.
     */
    public function load(ObjectManager $manager): void
    {
        $faker=Base::class;
        $user = $this->entityManager->getRepository(User::class)->find(1);
        $produit = $this->entityManager->getRepository(Produit::class)->find(6);
        

        for($i=0; $i < 5; ++$i){
            
            $commande = new Commande();

            $commande->setQuantity($faker::randomDigit());
            $commande->setTotal($faker::randomNumber(3));
            $commande->setState('En cours');
    
            $commande->setUser($user);
            $commande->addProduct($produit);
    
            $commande->setCreatedAt((new DateTime()));
            $commande->setUpdatedAt((new DateTime()));
        
    
            $manager->persist($commande);
        }//end for()

        $manager->flush();
    }//end load()
}
