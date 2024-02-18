<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\ParticipantRepository;
use App\Form\ParticipantType;

use App\Entity\Participant;

use App\Repository\UserRepository;
use App\Entity\User;


class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/afficheparticipant', name: 'app_afficheparticipant')]
    public function affiche(ParticipantRepository $repository): Response
    {
        $participants = $repository->findAll();
        return $this->render('participant/affiche.html.twig', ['participants' => $participants]);
    }

    #[Route('/showparticipant/{id}', name: 'app_detailparticipant')]
    public function showParticipant($id, ParticipantRepository $repository): Response
    {
        $participant = $repository->find($id);
        if (!$participant) {
            return $this->redirectToRoute('app_afficheparticipant');
        }

        return $this->render('participant/show.html.twig', ['participant' => $participant]);
    }

    #[Route('/addparticipant', name: 'app_addparticipant')]
    public function addParticipant(Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('app_afficheparticipant');
        }

        return $this->render('participant/add.html.twig', ['f' => $form->createView()]);
    }

    
    #[Route('/deleteparticipant/{id}', name: 'app_deleteparticipant')]
    public function deleteParticipant($id, ParticipantRepository $repository): Response
    {
        $participant = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_afficheparticipant');
    }
}
