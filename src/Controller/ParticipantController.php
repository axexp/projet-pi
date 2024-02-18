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

        // Remove the participant from the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($participant);
        $entityManager->flush();

        // Redirect to the app_eventDetails route after successful deletion
        return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
    }

    
}
