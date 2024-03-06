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

//use App\Controller\Pdf;
use Knp\Snappy\Pdf;

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
#[Route('/generate-pdf', name: 'app_generate_pdf')]
    public function generatePdf(Pdf $pdf): Response
    {
        // Fetch all events from your repository
        $events = $this->getDoctrine()->getRepository(Event::class)->findBy([], ['datedebut' => 'ASC']);

        // Render the events to HTML (assuming you have a 'pdf.html.twig' template)
        $html = $this->renderView('event/pdf.html.twig', [
            'event' => $events,
        ]);

        // Generate PDF
        $filename = 'events_' . date('Ymd_His') . '.pdf';
        $response = new Response($pdf->getOutputFromHtml($html), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);

        return $response;
    }
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
/*
#[Route('/affiche', name: 'app_Affiche')]
public function affiche(Request $request, EventRepository $eventRepository): Response
{
    if ($request->isXmlHttpRequest()) {
        $currentPage = $request->request->getInt('start', 1);
        $itemsPerPage = $request->request->getInt('length', 5);
        $offset = ($currentPage - 1) * $itemsPerPage;
        $searchQuery = $request->request->get('search')['value'];

        $events = $eventRepository->searchEvents($searchQuery, $itemsPerPage, $offset);
        $totalItems = $eventRepository->countFilteredEvents($searchQuery);

        $response = [
            'data' => $events,
            'recordsTotal' => $totalItems,
            'recordsFiltered' => $totalItems,
            'draw' => $request->request->getInt('draw', 1),
        ];

        return $this->json($response);
    }

    $currentPage = $request->query->getInt('page', 1);
    $itemsPerPage = 5;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $searchQuery = $request->query->get('search');
    $events = $eventRepository->searchEvents($searchQuery, $itemsPerPage, $offset);
    $totalItems = $eventRepository->countFilteredEvents($searchQuery);
    $totalPages = ceil($totalItems / $itemsPerPage);

    return $this->render('event/afficheback.html.twig', [
        'event' => $events,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'searchQuery' => $searchQuery,
    ]);
}


#[Route('/affiche', name: 'app_Affiche')]
    public function affiche(Request $request, EventRepository $eventRepository): Response
    {
        $em = $this->getDoctrine()->getManager();

        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Utilisez la méthode findBy pour obtenir les événements paginés
        $events = $eventRepository->findBy([], ['datedebut' => 'ASC'], $itemsPerPage, $offset);

        // Utilisez la méthode count pour obtenir le nombre total d'événements
        $totalItems = $eventRepository->count([]);

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('event/afficheback.html.twig', [
            'event' => $events,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
*/

#[Route('/affiche', name: 'app_Affiche')]
public function affiche(Request $request, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();

    $currentPage = $request->query->getInt('page', 1);
    $itemsPerPage = 5;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $searchQuery = $request->query->get('search', '');

    $eventRepository = $em->getRepository(Event::class);

    if ($searchQuery !== '') {
        $eventItems = $eventRepository->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
        $totalItems = count($eventItems); 
    } else {
        $eventItems = $eventRepository->findBy([], null, $itemsPerPage, $offset);
        $totalItems = $eventRepository->count([]); 
    }

    //if ($totalItems == 0) {
    //$totalPages = 1 ;
    //} else { 
        $totalPages = ceil($totalItems / $itemsPerPage);
    //}

    return $this->render('event/afficheback.html.twig', [
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

        return $this->render('event/Detailback.html.twig', [
            'event' => $event,
           
        ]);
    }



#[Route('/add', name: 'app_Add')]
    public function add(Request $request): Response
    {
        $event = new Event();
        $event->setNbPlacesR(0);
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

        return $this->render('event/Addback.html.twig', [
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

    return $this->render('event/editback.html.twig', [
        'event' => $event,
        'f' => $form->createView(),
    ]);
}


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
public function showb($id, UserRepository $userRepository,Request $request,EventRepository $repository)
{
    $searchQuery = $request->query->get('search', '');

    $user = $userRepository->find($id);

    //$repository = $this->getDoctrine()->getRepository(Event::class);
    $events = $searchQuery !== '' ?
        $repository->findBySearchQuery($searchQuery) :
        $repository->findAll();

    return $this->render('event/show.html.twig', [
        'event' => $events,
        'user' => $user,
        'searchQuery' => $searchQuery,
    ]);
}


#[Route('/eventDetails/{id}/{userId}', name: 'app_eventDetails')]
public function eventDetails($id, $userId, EventRepository $eventRepository, UserRepository $userRepository)
{
    
    $event = $eventRepository->find($id);
    $user = $userRepository->find($userId);
    //$dateExpired = date('Y-m-d H:i:s');

    if (!$event || !$user) {
        throw $this->createNotFoundException('Event or User not found');
    }

    //$comments = $event->getComments();

    return $this->render('event/showdetail.html.twig', [
        'event' => $event,
        'user' => $user,
        // 'comments' => $comments,
        //'dateExpired' => $dateExpired,
    ]);
}


/************************************************************************************************************************************************************** */







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



}
