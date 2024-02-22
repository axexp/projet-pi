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

        // Decrement nbPlacesR by 1
        $event->setNbPlacesR($event->getNbPlacesR() - 1);

        // Remove the participant from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->persist($event); // Persist the updated event
        $entityManager->flush();

        // Optionally add a flash message
        $this->addFlash('success', 'Participant has been deleted.');

        // Redirect to the app_ShoweventA route after successful deletion
        return $this->redirectToRoute('app_ShoweventA', ['id' => $idevent]);
    }



/******************************************************************************************************************************************* */
/*********************************************************client********************************************************************************** */

    #[Route('/deleteparticipant/{ref}/{idevent}/{iduser}', name: 'app_deleteparticipant')]
    public function deleteParticipant($ref, $idevent, $iduser, ParticipantRepository $repository, UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        // Find the participant based on the provided parameters
        $participant = $repository->findOneBy(['event' => $idevent, 'user' => $iduser]);

        if (!$participant) {
            // Handle the case when the participant is not found, e.g., show an error message or redirect
            // You may want to adjust this part based on your application's requirements
            return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
        }

        $event = $eventRepository->find($idevent);
        // Decrement nbPlacesR by 1
        $event->setNbPlacesR($event->getNbPlacesR() - 1);


        // Remove the participant from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->persist($event); // Persist the updated event
        $entityManager->flush();

        // Redirect to the app_eventDetails route after successful deletion
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

        

        // Assuming you have a OneToMany relationship between Event and Participant
        $participant = new Participant();
        $participant->setEvent($event);
        $participant->setUser($user);

        //increlent the nblpace occupe in the event attribut
        // nbPlacesR by 1
        $event->setNbPlacesR($event->getNbPlacesR() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($participant);
        $em->persist($event); // Persist the updated event
        $em->flush();

        // Check if the event object is not null before accessing its id property
         $eventId = $event ? $event->getId() : null;

        if ($eventId === null) {
        throw $this->createNotFoundException('Event ID is null');
        }

        // You can redirect to the event details page or any other page
        return $this->redirectToRoute('app_eventDetails', ['id' => $eventId, 'userId' => $userId]);
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

    
/*
    
    #[Route('/deleteparticipant/{ref}/{idevent}/{iduser}', name: 'app_deleteparticipant')]
public function deleteParticipant($ref, $idevent, $iduser, ParticipantRepository $repository, UserRepository $userRepository, EventRepository $eventRepository): Response
{
    
    
    // Find the user by ID
    $user = $userRepository->find($iduser);

    // Find the event by ID
    $event = $eventRepository->find($idevent);

    // Check if the event exists
    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    // Check if the user exists
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    // Get all participants
    $participants = $repository->findAll();

    // Flag to check if at least one participant belongs to the specified user
    $userFound = false;

    // Iterate through participants
    foreach ($participants as $participant) {
        // Check if the participant's user reference matches
        if ($participant->isUserRefEqual($ref)) {
            // Perform the action for participants belonging to the specified user
            $this->getParticipant($participant);
            
            // Set the flag to true since at least one participant belongs to the user
            $userFound = true;
            
        }
    }

    if (userFound)
    // Remove the participant and flush changes
    $em = $this->getDoctrine()->getManager();
    $em->remove($participant);
    $em->flush();

    return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
}
*/


    
}
