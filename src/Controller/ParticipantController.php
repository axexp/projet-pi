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

use App\Repository\EventRepository;


class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }



/******************************************************************************************************************************************* */
/*********************************************************respensable********************************************************************************** */




    #[Route('/deleteparticipantA/{ref}/{idevent}', name: 'app_deleteparticipantA')]
    public function deleteParticipantA($ref, $idevent, ParticipantRepository $participantRepository, EventRepository $eventRepository): Response
    {
        $participant = $participantRepository->find($ref);
        $event = $eventRepository->find($idevent);

        if (!$participant || !$event) {
            throw $this->createNotFoundException('Participant or Event not found');
        }

        $event->setNbPlacesR($event->getNbPlacesR() - 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->persist($event);
        $entityManager->flush();

        $this->addFlash('success', 'Participant has been deleted.');

        
        return $this->redirectToRoute('app_ShoweventA', ['id' => $idevent]);
    }



/******************************************************************************************************************************************* */
/*********************************************************client********************************************************************************** */

    #[Route('/deleteparticipant/{ref}/{idevent}/{iduser}', name: 'app_deleteparticipant')]
    public function deleteParticipant($ref, $idevent, $iduser, ParticipantRepository $repository, UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        
        $participant = $repository->findOneBy(['event' => $idevent, 'user' => $iduser]);
/*
        if (!$participant) {

            return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
        }
*/
        $event = $eventRepository->find($idevent);
        
        $event->setNbPlacesR($event->getNbPlacesR() - 1);


       
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->persist($event); 
        $entityManager->flush();

        return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
    }



    #[Route('/addParticipant/{id}/{userId}', name: 'app_addParticipant')]
    public function addParticipant(int $id, int $userId, EventRepository $eventRepository, UserRepository $userRepository): Response
    {
        $event = $eventRepository->find($id);
        $user = $userRepository->find($userId);

        if (!$event || !$user) {
            throw $this->createNotFoundException('Event or User not found');
        }


        $participant = new Participant();
        $participant->setEvent($event);
        $participant->setUser($user);


        $event->setNbPlacesR($event->getNbPlacesR() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($participant);
        $em->persist($event); 
        $em->flush();

/*
         $eventId = $event ? $event->getId() : null;

        if ($eventId === null) {
        throw $this->createNotFoundException('Event ID is null');
        }
*/
        
        return $this->redirectToRoute('app_eventDetails', ['id' => $id, 'userId' => $userId]);
    }






/************************************************************************************************************************************************* */
/**************************************************************CRUD-PARTICIPANT*********************************************************************************** */

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
    public function addParticipantc(Request $request): Response
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

    

    
}
