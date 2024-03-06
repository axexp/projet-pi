<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\UserRepository;
use App\Repository\UserRepository as RepositoryUserRepository;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(CommandeREpository $comr,SessionInterface $s,PanierRepository $pr,RepositoryUserRepository $rep): Response
    {
        $iduser=$s->get('name');
        $idpanier=$pr->findOneBy(['username'=>$iduser,'status'=>'actif']);
        $comisfound=$comr->findOneBy(['idpanier'=>$idpanier]);
        $user=$rep->findoneby(['name'=>$iduser])
        ;

        $panier=$idpanier;
        $objpanier=$panier;
        $idc=$comisfound->getId();
        // $panier=$comr->findOneBy(['idpanier'=>$panier->getId()]);
       $panier=$panier->getPanierproducts()->toArray();
      
       return $this->render('commande/index.html.twig',['panier'=>$panier,'objpanier'=>$objpanier,'user'=>$user]);

    }



    #[Route('/addcommande/{idp}', name: 'app_commande_add')]
    public function addcommande(SessionInterface $s,PanierRepository $pr,$idp,ManagerRegistry $em,CommandeRepository $comr): Response
    {
        
        $panier_is_found=$pr->findOneBy(['id'=>$idp]);

 $comanfound=$comr->findOneBy(['idpanier'=>$panier_is_found->getId()]);
if($comanfound)
return $this->redirectToRoute('app_commande');
        if($panier_is_found)
        {


        //        $com=$comr->find($idp); 
          //      $com=$comr->findOneBy(['idpanier'=>$idp]);
          $commande=new Commande();
          $commande->setAdresse("adress is not set yet");
          $commande->setIdpanier($panier_is_found);
          $commande->setTotal($panier_is_found->getTotal());
          $commande->setEtat("Pending");
          $em=$em->getManager();
          $em->persist($commande);
          $em->flush($commande);
        return $this->redirectToRoute('app_commande');          //          return $this->redirectToRoute('remove_all');
        }

    }
//}
#[Route('/changestatus/{idp}', name: 'commande_change_status')]
public function changeetat(SessionInterface $s,PanierRepository $pr,ManagerRegistry $em,CommandeRepository $comr,$idp): Response
{
    $com_is_found=$comr->findOneBy(['etat'=>'Pending','idpanier'=>$idp]);

    if($com_is_found)
    {


    //        $com=$comr->find($idp); 
      //      $com=$comr->findOneBy(['idpanier'=>$idp]);
    $panier=$com_is_found->getIdpanier();
      $panier->setStatus('nonactif');
    $com_is_found->setIdpanier($panier);
      $com_is_found->setEtat("In PROGRESS");
      $em=$em->getManager();
      $em->persist($com_is_found);
      $em->flush($com_is_found);
      return New Response("your  commande has been updated it status");

      //          return $this->redirectToRoute('remove_all');
    }
    return New Response("your  commande has not been found");

}

//

        
       
        // 
    //    return $this->render('commande/index.html.twig');

#[Route('/removecommande/{idp}', name: 'app_commande_remove')]
    public function removecommande(SessionInterface $s,PanierRepository $pr,$idp,ManagerRegistry $em,CommandeRepository $comr): Response
    {
        //$com=$comr->find($idp); 
        $com=$comr->findOneBy(['idpanier'=>$idp]);
        $em=$em->getManager();
      //  $com = $comr->findOneBy(['idpanier' => $idp]);
       // $em = $em->getManager();
    
    
       //changing the status to done  code need to be fixed asap
        if (!empty($com)) {
            try {
                $em->remove($com);
                $em->flush();
                return new Response("Command removed successfully.");
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                return new Response("Cannot delete the command because it's associated with other records.");
            }
        } else {
            return new Response("You do not have an order yet. Please check the store.");
        }
        // 
    //    return $this->render('commande/index.html.twig');
}

#[Route('/showcommandsinprogress', name: 'commande_show_progress')]
public function showcom(SessionInterface $s,PanierRepository $pr,ManagerRegistry $em,CommandeRepository $comr,RepositoryUserRepository $rep): Response
{
    $em = $em->getManager();
    $iduser = $s->get('name');
    $idpaniers = $pr->findBy(['username' => $iduser, 'status' => 'nonactif']);
    $comisfounds = [];
    $user=$rep->findoneby(['name'=>$iduser])
        ;

    foreach ($idpaniers as $idpanier) {

        $comisfound = $comr->findOneBy(['idpanier' => $idpanier->getId(), 'etat' => ['In PROGRESS','shipped','Road','done']]);
        if ($comisfound) {

            $comisfounds[] = $comisfound;
        }
    }
    // Pass the $comisfounds array to Twig for rendering
    return $this->render('commande/order.html.twig', [
        'comisfounds' => $comisfounds,
        'user'=> $user,
    ]);
}


#[Route('/admincom', name: 'show_command_for_admin')]
public function aff(CommandeRepository $comr,Request $request,ManagerRegistry $em): Response
{
    $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
            $em =$em->getManager();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();

            return $this->redirectToRoute('commande_success'); // Redirect to a success page
        }

    
    


return $this->render('commande/admin.html.twig',['com'=>$comr->findAll(),'form'=>$form->createView()]);
}



#[Route('/admincom/{id}', name: 'edit_command_for_admin')]
public function edit($id,CommandeRepository $comr,Request $request,ManagerRegistry $em): Response
{
    
    $commande = $comr->find($id);
        //$form = $this->createForm(CommandeType::class, $commande);
            $em =$em->getManager();

        //$form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $etat = $request->request->get('etat');

            
            
            $commande->setEtat($etat);

            $em->persist($commande);
            $em->flush();

            return $this->redirectToRoute('show_command_for_admin'); // Redirect to a success page
        }

    
    


return $this->render('commande/admin.html.twig',['com'=>$comr->findAll()]);
}





}