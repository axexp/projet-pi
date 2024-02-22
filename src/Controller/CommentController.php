<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CommentRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\EventRepository;

use App\Entity\Comment;
use App\Form\CommentType;

use App\Repository\UserRepository;
use App\Entity\User;


class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

/******************************************************************************************************************************************* */
/*********************************************************respensable********************************************************************************** */


    #[Route('/deletecommentA/{ref}/{idevent}', name: 'app_deletecommentA')]
    public function deleteCommentA($ref, $idevent, CommentRepository $commentRepository, EventRepository $eventRepository)
    {
        
        $comment = $commentRepository->find($ref);
        $event = $eventRepository->find($idevent);
         
        if (!$comment || !$event) {
            throw $this->createNotFoundException('Comment or Event not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
    
        //return $this->render('event/DetailA.html.twig', ['event' => $event]);
        return $this->redirectToRoute('app_ShoweventA', ['id' => $idevent]);
    }




/******************************************************************************************************************************************* */
/*********************************************************client********************************************************************************** */


    #[Route('/Addcomment/{idevent}/{iduser}', name: 'app_Addcomment_event')]
    public function AddCommentToEvent(int $idevent, int $iduser, Request $request, EventRepository $eventRepository, CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $event = $eventRepository->find($idevent);
        $user = $userRepository->find($iduser);

        if (!$event || !$user) {
            throw $this->createNotFoundException('Event or User not found');
        }

        if ($request->isMethod('POST')) {
            // Handle form submission
            $commentaire = $request->request->get('commentaire');

            // Create a new comment
            $comment = new Comment();
            $comment->setEvent($event);
            $comment->setCommentaire($commentaire);
            $comment->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // Return the updated event details with comments
            return $this->render('Event/showdetail.html.twig', [
                'event' => $event,
                'user' => $user,
            ]);
        }

    return $this->render('Event/addtest.html.twig', ['event' => $event]);
    }

    #[Route('/deletecomment/{ref}/{idevent}/{iduser}', name: 'app_deletecomment')]
    public function deleteComment($ref, $idevent, $iduser, CommentRepository $commentRepository, UserRepository $userRepository, EventRepository $eventRepository)
    {
        $comment = $commentRepository->find($ref);
        $user = $userRepository->find($iduser);
        $event = $eventRepository->find($idevent);
    
        if (!$comment || !$user || !$event) {
            throw $this->createNotFoundException('Comment, User, or Event not found');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
    
        return $this->redirectToRoute('app_eventDetails', ['id' => $idevent, 'userId' => $iduser]);
    }
    



/*********************************************************************************************************************************************** */
/*********************************************************CRUD-COMMENT************************************************************************************** */



#[Route('/editcomment/{id}', name: 'app_editcomment')]
public function edit($id, CommentRepository $repository, Request $request)
{
    $comment = $repository->find($id);
/*
    if (!$comment) {
        return $this->redirectToRoute('app_Affichecomment');
    }
*/
    $form = $this->createForm(UserType::class, $comment);
    $form->add('Modifier', SubmitType::class);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_Afficheuser');
    }

    return $this->render('user/Edit.html.twig', ['form' => $form->createView()]);
}


    #[Route('/Affichecomment', name: 'app_Affichecomment')]
    public function Affiche (CommentRepository $repository)
        {
            $comment=$repository->findAll() ; //select *
            return $this->render('comment/Affiche.html.twig',['comment'=>$comment]);
        }


    #[Route('/Showcomment/{ref}', name: 'app_detailcomment')]
    public function showComment($ref, CommentRepository $repository)
    {
        $comment = $repository->find($ref);
        if (!$comment ) {
            return $this->redirectToRoute('app_Affichecomment');
        }

        return $this->render('comment/show.html.twig', ['b' => $comment]);
    }



    

    #[Route('/Addcomment', name: 'app_Addcomment')]
    public function Add(CommentRepository $repository,Request $request)
    {
        $comment = new Comment();
        $form = $this->CreateForm(CommentType::class, $comment);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_Affichecomment');
            
        }
        return $this->render('Comment/Add.html.twig', ['f' => $form->createView()]);

    }




}
