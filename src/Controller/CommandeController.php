<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin")
 */
class CommandeController extends AbstractController
{

    /**
     * @Route("/voir-les-commandes", name="show_commandes", methods={"GET"})
     */
    public function showCommandes(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/show_commandes.html.twig', [
            'commandes' => $commandeRepository->findBy(['deletedAt' => null])
        ]);
    }

    /**
     * @Route("/archiver-une-commande/{id}", name="soft_delete_commande", methods={"GET"})
     */
    public function softDeleteCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setDeletedAt(new DateTime());
        $commande->setState('annulée');

        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', "La commande #". $commande->getID() . " a bien été annulée");
        return $this->redirectToRoute('show_commandes');

    }

    /**
     * @Route("/voir-les-commandes-annulees", name="show_canceled_commandes", methods={"GET"})
     */
    public function showCanceledCommandes(CommandeRepository $commandeRepository): Response
    {
        //Utilisation de la méthode créée dans CommandeRepository pour les commandes annulées
        $canceledCommandes = $commandeRepository->findByCanceled();

        return $this->render('admin/trash/show_canceled_commandes.html.twig', [
            'canceled_commandes' => $canceledCommandes
        ]);

    }

}

















?>