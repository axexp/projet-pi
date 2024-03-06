<?php

namespace App\Controller;

use App\Form\QtpanierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierproductController extends AbstractController
{
    #[Route('/st', name: 'app_panierproduct')]
    public function index(): Response
    {
        return $this->render('panierproduct/index.html.twig', [
            'controller_name' => 'PanierproductController',
        ]);
    }



    #[Route('/sx', name: 'app_panierproduc_mod_qtt')]

    public function modifyQuantity(Request $request): Response
    {
        $form = $this->createForm(QtpanierType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du formulaire (enregistrement ou autre action)
            // Redirection ou rendu de la vue appropriée
        }

        return $this->render('panier/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
