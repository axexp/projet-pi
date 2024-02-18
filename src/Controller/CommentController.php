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


    #[Route('/deletecomment/{ref}', name: 'app_deletecomment')]
    public function delete($ref, CommentRepository $repository)
    {
        $comment = $repository->find($ref);


        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();


        return $this->redirectToRoute('app_Affichecomment');
    }


    


}
