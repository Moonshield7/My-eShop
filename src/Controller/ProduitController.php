<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/admin")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/voir-les-produits", name="show_produit", methods={"GET"})
     */
    public function showProduit(ProduitRepository $produitRepository): Response
    {
        return $this->render("admin/show_produit.html.twig", [
            'produits' => $produitRepository->findby(['deletedAt' => null]),
        ]);
    }

    /**
     * @Route("/créer-un-produit", name="create_produit", methods={"GET|POST"})
     */
    public function createProduit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $produit->setCreatedAt(new DateTime());
            $produit->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if($photo){

                $this->handleFile($produit, $photo, $slugger);

            } //end if($photo)

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouveau produit est ajouté !');
            return $this->redirectToRoute('show_produit');

        } //end if($form)

        return $this->render('admin/form/form_produit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-un-produit_{id}", name="update_produit", methods={"GET|POST"})
     */
    public function updateProduit(Produit $produit, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {
        $originalPhoto = $produit->getPhoto();

        $form = $this->createForm(ProduitType::class, $produit, [
            'photo' => $originalPhoto
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $produit->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if($photo){
                $this->handleFile($produit, $photo, $slugger);
            }
            else {
                $produit->setPhoto($originalPhoto);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez modifié le produit avec succès !');
            return $this->redirectToRoute('show_produit');
        }


        return $this->render('admin/form/form_produit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }


    // ################# PRIVATE FUNCTION #################

    private function handleFile(Produit $produit, UploadedFile $photo, SluggerInterface $slugger) : Void
    {
        $extension = '.' . $photo->guessExtension();
        $safeFilename = $slugger->slug($produit->getTitle());

        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        try{
            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            $produit->setPhoto($newFilename);
        } catch (FileException $exception) {
            $this->addFlash('warning', "La photo du produit ne s'est pas importée avec succès. Veuillez réessayer en modifiant le produit.");
        } //end catch()

        // return $produit;
    }

    /**
     * @Route("/archiver-un-produit/{id}", name="soft_delete_produit", methods={"GET"})
     */
    public function softDeleteProduit(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        // Côté mirroir de la bascule (on/off)
        $produit->setDeletedAt(null);

        $entityManager->persist($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit '. $produit->getTitle() . ' a bien été archivé.');
        return $this->redirectToRoute('show_produit');
    }

    /**
     * @Route("/restaurer-un-produit/{id}", name="restoreProduit", methods={"GET"})
     */
    public function restoreProduit(Produit $produit, EntityManagerInterface $entityManager): Response
    {

        $entityManager->persist($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit '. $produit->getTitle() . ' a bien été restauré.');
        return $this->redirectToRoute('show_produit');
    }

    

}







?>