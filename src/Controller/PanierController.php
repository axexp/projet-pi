<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Panierproduct;
use App\Repository\CommandeRepository;
use App\Repository\PanierproductRepository;
use App\Repository\PanierRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierRepository $panr,SessionInterface $s,PanierproductRepository $ppr,CommandeRepository $comr,ManagerRegistry $em,UserRepository $rep): Response
{
    $em=$em->getManager();
    $iduser=$s->get('name');
    //$user=$repo->findOneBy(['name'=>$iduser]);
    $panier=$panr->findOneBy(['username'=>$iduser,'status'=>'actif']);
    $user=$rep->findoneby(['name'=>$iduser])
        ;
if($panier)
{

        $objpanier=$panier;
         $panprod= $ppr->findBy(['idpanier'=>$panier->getId()]);
            if($panprod)
                {
                    $panier =$panier->getPanierproducts()->toArray();             
                    return $this->render('panier/index.html.twig', ['panier'=>$panier ,'objpanier'=>$objpanier ,'user'=>$user]);
                } 
            else
            return $this->redirectToRoute('app_product');

 }
 else{
    //return new Response("vous  n'avez pas encore d'un panier");
    // $panier = new Panier();
    // $panier->setUsername($iduser);
    // $panier->setTotal(0);
    // $panier->setStatus('actif');
    // $em->persist($panier);
    // $em->flush();
     return $this->redirectToRoute('app_product');


    }

/*

    $iduser=$s->get('name');


    $listpan=$panr->findBy(['username'=>$iduser]);

    $foundedcommande = $comr->findOneBy(['idpanier' => $panier->getId(), 'etat' => 'In PROGRESS']);

    if ($foundedcommande) {

        $panier=$panr->findOneBy(['id'=>$foundedcommande->getIdpanier()]);
        $objpanier=$panier;
        $panprod= $ppr->findBy(['idpanier'=>$panier->getId()]);
        if($panprod)
            {
                $panier =$panier->getPanierproducts()->toArray();
            
                return $this->render('panier/index.html.twig', ['panier'=>$panier ,'objpanier'=>$objpanier]);
            } 
           else
         
           return new Response("votre panier est vide");
    
     }
*/
    }

    


    #[Route('/addpanier/{id}', name: 'app_add')]
public function addProductToPanier(ProductRepository $productRepository, $id, SessionInterface $s, PanierRepository $panr,ManagerRegistry $em,PanierproductRepository $ppr): Response
{
    
    /*if (!$product) {
        $this->addFlash('error', 'Product not found.');
        return $this->redirectToRoute('app_product');
    }
    else
    {
        $q=$product->getQt();
        $q++;
        $product->setQt($q);
    }*/
    $product = $productRepository->find($id);
    $iduser = $s->get('name');
    $panier = $panr->findOneBy(['username' => $iduser,'status'=>'actif']);
    $em = $em->getManager();


    if (!$panier) 
    {
//        dd("panier exist");
        $panier = new Panier();
        $panier->setUsername($iduser);
        $panier->setTotal(0);
        $panier->setStatus('actif');
        $em->persist($panier);
        $em->flush();
        

        
       // return $this->redirectToRoute('app_add',['id'=>$id]);
    }

  // return $this->redirectToRoute('',['idp'=>$panier->getId()]);

       
       
        $isfound=  $ppr->findOneBy(['idproduct'=>$id,'idpanier'=>$panier->getId()]);
//  echo $isfound->getIdpanier()->getId();
        if($isfound)
            {
//dd("is found");
$panier=$isfound->getIdpanier();

                echo $isfound->getTotal();
                $q=$isfound->getQt();
               //echo " before ".$q;
                $q+=1;
               // echo " after".$q;
               // dd($q);
                $isfound->setQt($q);
               //  $em->persist($panprod);
                //$em->persist($product);
               // $total=$isfound->getTotal();
                $total=$isfound->getQt()*$isfound->getIdproduct()->getPrice();
                $isfound->setTotal($total);              
                $panier->setTotal($panier->getTotal()+$total); 
                $em->persist($panier);
                $em->persist($isfound);
                
                $em->flush(); 
                return $this->redirectToRoute('app_panier');
                //  return $this->redirectToRoute('app_panier',['idpanier'=>$panier->getId()]);

            }
        else
            {
                $panprod=new Panierproduct();
                $panprod->setIdpanier($panier);
                $panprod->setIdproduct($product);        
                $panprod->setQt(1);  
                $total=$panprod->getQt()*$product->getPrice(); 
            }
       
           
      
        $panprod->setTotal($total);
        $panier->addPanierproduct($panprod);
        $panier->setTotal($panier->getTotal()+$total);
        $em->persist($panprod);
        $em->persist($panier);
        //$em->persist($product);
        
        $em->flush(); 
      //  return New Response("after finsish");
    //$panier->addPanierproduct($);
    
    
        
  
       /* $total=$panprod->getTotal();
        $panprod->setTotal($total);
        $panier->addPanierproduct($panprod);
        $pantot=$panier->getTotal()+$total;
        $panier->setTotal($pantot);
    
    }
*/
   /* else
    {     
        //$panier->addPanierproduct($product);
 
    }
    foreach ($panier->getPanierproducts() as $prod)
    {
         $tot+=$prod->getQt()();
    }
$panier->setTotal($tot);
*/
// Flush after adding all products

    // Redirect to the panier page after adding the product
     return $this->redirectToRoute('app_panier');
}

//
#[Route('/removeoneFrompanier/{id}', name: 'app_down')]
public function RemoveOneProductFromPanier(ProductRepository $productRepository, $id, SessionInterface $s, PanierRepository $panr,ManagerRegistry $em,PanierproductRepository $ppr): Response
{
    $em = $em->getManager();

    $iduser = $s->get('name');  
    $panier = $panr->findOneBy(['username' => $iduser,'status'=>'actif']);

    $isfound=$ppr->findOneBy(['idproduct'=>$id,'idpanier'=>$panier->getId()]);
    if($isfound->getQt()>1)
                {
                    $panier->setTotal($panier->getTotal()-$isfound->getTotal());

                    $isfound->setQt($isfound->getQt()-1);

                }
                else{
                    $panier->setTotal($panier->getTotal()-$isfound->getTotal());

                   $em->remove($isfound);
                   // $panier->removePanierproduct($isfound);
                }
                $em->persist($panier);
                $em->flush();
              
                    
  /*  
    
    
    $product = $productRepository->find($id);
    $iduser = $s->get('name');
    
    if (!$product) {
        $this->addFlash('error', 'Product not found.');
        return $this->redirectToRoute('app_product');
    }
    else if($product->getQt()>1)
    {
        $q=$product->getQt();
        $q--;
        $product->setQt($q);
        $panier = $panr->findOneBy(['username' => $iduser]);
        $em = $em->getManager();
        $em->persist($product);
        
        $em->flush(); // Flush after adding all products
    
    }
    else{
        return $this->redirectToRoute('app_remove');

    }

    */
    // Redirect to the panier page after adding the product
    return $this->redirectToRoute('app_panier');
}

//
#[Route('/removefrompanier/{id}', name: 'app_remove')]
public function RemoveProductFromPanier(PanierproductRepository $ppr, $id, SessionInterface $s, PanierRepository $panr,ManagerRegistry $em): Response
{

   // $product = $productRepository->find($id);

   $em = $em->getManager();

   $iduser = $s->get('name');  
   $panier = $panr->findOneBy(['username' => $iduser,'status'=>'actif']);

   $isfound=$ppr->findOneBy(['idproduct'=>$id,'idpanier'=>$panier->getId()]);

   if($isfound)
             {              

                $em->remove($isfound);
                $panier->setTotal($panier->getTotal()-$isfound->getTotal());
                // $panier->removePanierproduct($isfound);
                $em->persist($panier);
                $em->flush();
 
             }
               
    // Redirect to the panier page after adding the product
   return $this->redirectToRoute('app_panier');
    // return $this->render('panier/index.html.twig', ['panier'=>$panier]); 
}
//
#[Route('/removepanier', name: 'remove_all')]
public function RemovePanier(ProductRepository $productRepository, SessionInterface $s, PanierRepository $panr,ManagerRegistry $em): Response
{

   // $product = $productRepository->find($id);

    $em=$em->getManager();
   $iduser = $s->get('name');
   $panier = $panr->findOneBy(['username' => $iduser,'status'=>'actif']);

    if (!empty($panier)) {
        foreach ($panier->getPanierproducts() as $isfound)
        {
              //  $product->setQt(0);
                 $panier->setTotal($panier->getTotal()-$isfound->getTotal());
                 $em->remove($isfound); 
        }
      
        }
        $em->flush();
    
    

 
    
    return $this->redirectToRoute('app_panier');

}

 
    // Redirect to the panier page after adding the product
    // return $this->render('panier/index.html.twig', ['panier'=>$panier]); 
}

//


/*
#[Route('/show/{id}', name: 'app_show')]
    public function displayPanier(PanierRepository $panierRepository, $id): Response
    {
        $panier = $panierRepository->find($id);

        if (!$panier) {
 
            $this->addFlash('error', 'Panier not found.');
            return $this->redirectToRoute('app_panier');
        }

        $listProducts = $panier->getListProducts();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'products' => $listProducts,
        ]);
    }
*/


