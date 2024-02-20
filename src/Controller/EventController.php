<?php

namespace App\Controller;

use App\Form\EventType;
use App\Entity\Event;
use App\Repository\EventRepository;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

use App\Repository\CommentRepository;
use App\Entity\Comment;

use App\Repository\UserRepository;
use App\Entity\User;

use App\Repository\ParticipantRepository;
use App\Entity\Participant;

use Doctrine\Persistence\ManagerRegistry;



class EventController extends AbstractController
{
    
    #[Route('/event', name: 'app_event')]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

/***************************************************************************responsable*************************************************************************** */
/*       original
#[Route('/affiche', name: 'app_Affiche')]
public function affiche(Request $request)
{
    $searchQuery = $request->query->get('search', '');

    $repository = $this->getDoctrine()->getRepository(Event::class);
    $events = $searchQuery !== '' ?
        $repository->findBySearchQuery($searchQuery) :
        $repository->findAll();

    return $this->render('event/affiche.html.twig', [
        'event' => $events,
        'searchQuery' => $searchQuery,
    ]);
}
*/

#[Route('/affiche', name: 'app_Affiche')]
public function affiche(Request $request, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();

    $currentPage = $request->query->getInt('page', 1);
    $itemsPerPage = 2;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $searchQuery = $request->query->get('search', '');

    $eventRepository = $em->getRepository(Event::class);

    if ($searchQuery !== '') {
        $eventItems = $eventRepository->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
        $totalItems = count($eventItems); // Count the search results
    } else {
        $eventItems = $eventRepository->findBy([], null, $itemsPerPage, $offset);
        $totalItems = $eventRepository->count([]); // Count all items
    }

    $totalPages = ceil($totalItems / $itemsPerPage);

    return $this->render('event/affiche.html.twig', [
        'event' => $eventItems,
        'searchQuery' => $searchQuery,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
    ]);
}


#[Route('/ShoweventA/{id}', name: 'app_ShoweventA')]
    public function showeventA($id, EventRepository $repository)
    {
        $event = $repository->find($id);
        if (!$event) {
            return $this->redirectToRoute('app_Affiche');
        }

        return $this->render('event/DetailA.html.twig', ['event' => $event]);
    }



#[Route('/add', name: 'app_Add')]
    public function add(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();

            if ($imageFile instanceof UploadedFile) {
                $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('image_directory'), $newFilename);
                $event->setImage($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('app_Affiche');
        }

        return $this->render('event/Add.html.twig', [
            'f' => $form->createView(),
        ]);
    }


#[Route('/edit/{id}', name: 'app_editEvent')]
public function edit(EventRepository $repository, $id, Request $request): Response
{
    $event = $repository->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload
        $imageFile = $form->get('image')->getData();

        if ($imageFile instanceof UploadedFile) {
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('image_directory'), $newFilename);
            $event->setImage($newFilename);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('app_Affiche');
    }

    return $this->render('event/edit.html.twig', [
        'event' => $event,
        'f' => $form->createView(),
    ]);
}
/*
#[Route('/delete/{id}', name: 'app_deleteEvent')]
public function delete($id, EventRepository $repository, EntityManagerInterface $entityManager)
{
    $event = $repository->find($id);

    $comments = $event->getComments();

        foreach ($comments as $comment) {
              $entityManager->remove($comment);
        }

        $entityManager->remove($event);
        $entityManager->flush();
 

    $participants = $event->getParticipants();

        foreach ($participants as $participant) {
            $entityManager->remove($participant);
        }
        
        $entityManager->remove($event);
        $entityManager->flush();


    if (!$event) {
        throw $this->createNotFoundException('Event non trouvé');
    }

       $em = $this->getDoctrine()->getManager();
    $em->remove($event);
    $em->flush();

        
    return $this->redirectToRoute('app_Affiche');
}
*/

#[Route('/delete/{id}', name: 'app_deleteEvent')]
public function delete($id, EventRepository $repository, EntityManagerInterface $entityManager)
{
    $event = $repository->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    $comments = $event->getComments();
    foreach ($comments as $comment) {
        $entityManager->remove($comment);
    }

    $participants = $event->getParticipants();
    foreach ($participants as $participant) {
        $entityManager->remove($participant);
    }

    $entityManager->remove($event);
    $entityManager->flush();

    return $this->redirectToRoute('app_Affiche');
}




/*************************************************************************************************************************************************** */
/*************************************************************************client************************************************************************** */


#[Route('/showb/{id}', name: 'app_showb')]
public function showb($id, UserRepository $userRepository,Request $request)
{
    $searchQuery = $request->query->get('search', '');
    //$user = $userRepository->findAll();
    $user = $userRepository->find($id);

    $repository = $this->getDoctrine()->getRepository(Event::class);
    $events = $searchQuery !== '' ?
        $repository->findBySearchQuery($searchQuery) :
        $repository->findAll();

    return $this->render('event/show.html.twig', [
        'event' => $events,
        'user' => $user,
        'searchQuery' => $searchQuery,
    ]);
}

/*
#[Route('/show', name: 'app_show')]
public function show(Request $request)
{
    $searchQuery = $request->query->get('search', '');
    //$user = $userRepository->findAll();
    //$user = $userRepository->find($id);

    $repository = $this->getDoctrine()->getRepository(Event::class);
    $events = $searchQuery !== '' ?
        $repository->findBySearchQuery($searchQuery) :
        $repository->findAll();

    return $this->render('event/show.html.twig', [
        'event' => $events,
        //'user' => $user,
        'searchQuery' => $searchQuery,
    ]);
}
*/

#[Route('/eventDetails/{id}/{userId}', name: 'app_eventDetails')]
public function eventDetails($id, $userId, EventRepository $eventRepository, UserRepository $userRepository)
{
    // Assuming you want to fetch the event details from the database
    $event = $eventRepository->find($id);
    $user = $userRepository->find($userId);

    if (!$event || !$user) {
        throw $this->createNotFoundException('Event or User not found');
    }

    // Fetch the associated comments (assuming you have a relationship between Event and Comment)
    $comments = $event->getComments();

    return $this->render('event/showdetail.html.twig', [
        'event' => $event,
        'user' => $user,
        // 'comments' => $comments,
    ]);
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

    $em = $this->getDoctrine()->getManager();
    $em->persist($participant);
    $em->flush();

    // Check if the event object is not null before accessing its id property
    $eventId = $event ? $event->getId() : null;

    if ($eventId === null) {
        throw $this->createNotFoundException('Event ID is null');
    }

    // You can redirect to the event details page or any other page
    return $this->redirectToRoute('app_eventDetails', ['id' => $eventId, 'userId' => $userId]);
}




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



/************************************************************************************************************************************************************** */
/*
#[Route('/Addcomment/{id}', name: 'app_Addcomment_event')]
public function AddCommentToEvent(int $id, Request $request, EventRepository $eventRepository, CommentRepository $commentRepository)
{
    $event = $eventRepository->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    if ($request->isMethod('POST')) {
        // Handle form submission
        $commentaire = $request->request->get('commentaire');

        // Create a new comment
        $comment = new Comment();
        $comment->setEvent($event);
        $comment->setCommentaire($commentaire);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        //return $this->redirectToRoute('app_eventDetails');
        return $this->render('Event/showdetail.html.twig', ['event' => $event]);
    }

    return $this->render('Event/addtest.html.twig', ['event' => $event]);
}

*/


















/*************************************************************************************
#[Route('/eventDetails/{id}', name: 'app_eventDetails')]
public function eventDetails($id, EventRepository $eventRepository)
{
    // Assuming you want to fetch the event details from the database
    $event = $eventRepository->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    return $this->render('event/showdetail.html.twig', ['event' => $event]);
}

***************************************************************************/
/*
#[Route('/eventDetails/{id}/{userId}', name: 'app_eventDetails')]
public function eventDetails($id,$userId, EventRepository $eventRepository, UserRepository $userRepository)
{
    // Assuming you want to fetch the event details from the database
    $event = $eventRepository->find($id);
    $user = $userRepository->find($userId);

    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    // Fetch the associated user and comments
    $user = $this->getUser();
    $comments = $event->getComments();

    return $this->render('event/showdetail.html.twig', [
        'event' => $event,
        'user' => $user,
        //'comments' => $comments,
    ]);
}*/

/********************************************************************************************************************************************* */






/*

#[Route('/add', name: 'app_Add')]
public function  Add (Request  $request)
{
    $event=new Event();
    $form =$this->CreateForm(EventType::class,$event);
    //$form->add('Ajouter',SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid())
    {
        // Handle image upload
        $imageFile = $form->get('image')->getData();
        if ($imageFile instanceof UploadedFile) {
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('image_directory'), $newFilename);
            $event->setImage($newFilename);
        }

        $em=$this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();
        return $this->redirectToRoute('app_Affiche');
    }
    return $this->render('event/Add.html.twig',['f'=>$form->createView()]);

}





    #[Route('/edit/{id}', name: 'app_editEvent')]
    public function edit(EventRepository $repository, $id, Request $request)
    {
        $event = $repository->find($id);
        $form = $this->createForm(EventType::class, $event);
        //$form->add('Edit', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_Affiche");
        }

        return $this->render('event/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    // ...

#[Route('/add', name: 'app_Add')]
public function add(Request $request): Response
{
    $event = new Event();
    $form = $this->createForm(EventType::class, $event);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload
        $imageFile = $form->get('image')->getData();

        if ($imageFile instanceof UploadedFile) {
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('image_directory'), $newFilename);
            $event->setImage($newFilename);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        return $this->redirectToRoute('app_Affiche');
    }

    return $this->render('event/Add.html.twig', [
        'f' => $form->createView(),
    ]);
}
*/




/********************************************************************************************************************************************************* */
/*
#[Route('/add', name: 'app_Add')]
    public function add(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();

            if ($imageFile instanceof UploadedFile) {
                $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('image_directory'), $newFilename);
                $event->setImage($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('app_Affiche');
        }

        return $this->render('event/Add.html.twig', [
            'f' => $form->createView(),
        ]);
    }
*/

/************************************************************************************************************************************************************** */

// ...    

    



/*
    #[Route('/addcomment_direct/{id}', name: 'app_Addcomment_direct')]
    public function addCommentDirect($id, CommentRepository $commentRepository): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        // Create a new comment
        $comment = new Comment();
        $comment->setEvent($event);
        $comment->setCommentaire('Your comment here'); // You can set a default comment or leave it empty

        // Persist the comment to the database
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        // Redirect back to the event details page
        return $this->redirectToRoute('app_eventDetails', ['id' => $id]);
    }
*/




/*
#[Route('/addParticipant/{id}', name: 'app_addParticipant')]
    public function addParticipant(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        // Assuming you have a ManyToMany relationship between Event and Participant
        $participant = new Participant();
        $participant->setEvent($event);

        $em = $this->getDoctrine()->getManager();
        $em->persist($participant);
        $em->flush();

        // You can redirect to the event details page or any other page
        return $this->redirectToRoute('app_eventDetails', ['id' => $event->getId()]);
    }
*/
/*
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

    $em = $this->getDoctrine()->getManager();
    $em->persist($participant);
    $em->flush();

    // Check if the event object is not null before accessing its id property
    $eventId = $event->getId() ?? null;

    // You can redirect to the event details page or any other page
    return $this->redirectToRoute('app_eventDetails', ['id' => $event->getId()]);
}
*/




}
