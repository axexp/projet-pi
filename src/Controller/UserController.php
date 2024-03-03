<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Form\UserType;

use App\Entity\Comment;
use App\Form\CommentType;

use App\Entity\Participant;
use App\Form\ParticipantType;


use App\Form\EventType;
use App\Entity\Event;
use App\Repository\EventRepository;



use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

/***************************************************************************************************************************************** */
/*************************************************USER-TEST********************************************************************************** */

#[Route('/connect', name: 'app_connect')]
public function connect(AuthenticationUtils $authenticationUtils, Request $request): Response
{
    
    // Get the submitted name from the form
    $name = $request->request->get('name');

    // Check if the user with the provided username exists in the database
    $userRepository = $this->getDoctrine()->getRepository(User::class);
    $user = $userRepository->findOneBy(['name' => $name]);

    if (!$user) {
        // Handle the case when the user does not exist
        return $this->render('user/login.html.twig', [
            'error' => 'Invalid username',
        ]);
    }

    return $this->render('event/Homefront.html.twig', ['user' => $user, ]);

}


#[Route('/homefront/{id}', name: 'app_homefront')]
public function home($id, UserRepository $userRepository,Request $request): Response
{

    //$user = $userRepository->findAll();
    $user = $userRepository->find($id);
    
    return $this->render('event/Homefront.html.twig', ['user' => $user,]);
}

/*
#[Route('/homeback', name: 'app_homeback')]
public function homeback(Request $request): Response
{   
    
    return $this->render('event/Homeback.html.twig');
}*/


#[Route('/homeback', name: 'app_homeback')]
public function homeback(Request $request, EventRepository $eventRepository, UserRepository $userRepository): Response
{   
    
    $nbparticipants = $eventRepository->countAllParticipants();
    $nbevents = $eventRepository->countAllEvents();
    $nbcomments = $eventRepository->countAllComments();
    $nbusers = $userRepository->countAllUsers();
    $events = $eventRepository->findAll();


    return $this->render('event/Homeback.html.twig', [
        'nbparticipants' => $nbparticipants,
        'nbevents' => $nbevents,
        'nbcomments' => $nbcomments,
        'nbusers' => $nbusers,
        'events' => $events,
    ]);
}



/************************************************************************************************************************************************* */
/**************************************************************CRUD-USER*********************************************************************************** */


    #[Route('/Afficheuser', name: 'app_Afficheuser')]
    public function Affiche(UserRepository $repository)
    {
        $user = $repository->findAll();
        return $this->render('user/Affiche.html.twig', ['user' => $user]);
    }

    #[Route('/Showuser/{id}', name: 'app_detailuser')]
    public function showUser($id, UserRepository $repository)
    {
        $user = $repository->find($id);
        if (!$user) {
            return $this->redirectToRoute('app_Afficheuser');
        }

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    #[Route('/Adduser', name: 'app_Adduser')]
    public function Add(UserRepository $repository, Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_Afficheuser');
        }

        return $this->render('user/Add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/edituser/{id}', name: 'app_edituser')]
    public function edit($id, UserRepository $repository, Request $request)
    {
        $user = $repository->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_Afficheuser');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->add('Modifier', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_Afficheuser');
        }

        return $this->render('user/Edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/deleteuser/{id}', name: 'app_deleteuser')]
    public function delete($id, UserRepository $repository)
    {
        $user = $repository->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_Afficheuser');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_Afficheuser');
    }
}
