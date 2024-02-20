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
/*
    #[Route('/deletecommentA/{id}/{eventid}', name: 'app_deletecommentA')]
public function deleteCommentA(Request $request, int $id, int $eventid, CommentRepository $commentRepository): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Retrieve the event and comment entities
    $event = $entityManager->getRepository(Event::class)->find($eventid);
    $comment = $entityManager->getRepository(Comment::class)->find($id);

    // Check if both event and comment exist
    if (!$event || !$comment) {
        // Handle not found scenario or redirect as needed
        return $this->redirectToRoute('your_error_route');
    }

    // Check if the comment belongs to the event
    if ($comment->getEvent() !== $event) {
        // Handle invalid association or redirect as needed
        return $this->redirectToRoute('your_error_route');
    }

    // Remove the comment from the event and delete it
    $em = $this->getDoctrine()->getManager();
    $em->remove($comment);
    $em->flush();

    // Redirect to the event details page or any other page as needed
    //return $this->redirectToRoute('event_detail', ['id' => $event->getId()]);
    return $this->render('event/DetailA.html.twig', ['event' => $event]);
}

*/



/*********************************************************************************************************************************************** */
/*********************************************************CRUD-COMMENT************************************************************************************** */



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

         
    /*        $book1=$repository->findby(['title'=>$book->getTitle()]);
            if($book1){return $this->render('book/Exist.html.twig'); }
            //initialisation de l'attribut "published" a true
            //  $book->setPublished(true);
            // get the accociated author from the book entity
            $author = $book->getAuthor();
            
            //incrementation de l'attribut "nb_books" de l'entire Author
            //if ($author instanceof Author) {$author->setNbBooks($author->getNbBooks() + 1);}
            $author->setNbBooks($author->getNbBooks() + 1);                                         */

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_Affichecomment');
            
        }
        return $this->render('Comment/Add.html.twig', ['f' => $form->createView()]);

    }



/*
    #[Route('/deletecomment/{ref}/{id}/{userId}', name: 'app_deletecomment')]
    public function delete($ref,$id, $userId, CommentRepository $repository, EventRepository $eventRepository, UserRepository $userRepository)
    {
        $comment = $repository->find($ref);

        $event = $eventRepository->find($id);
        $user = $userRepository->find($userId);

        if (!$event || !$user) {
            throw $this->createNotFoundException('Event or User not found');
        }


        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();


        //return $this->redirectToRoute('app_Affichecomment',);
        return $this->render('event/showdetail.html.twig', [
            'event' => $event,
            'user' => $user,
            // 'comments' => $comments,
        ]);
    }

    */


    


}
