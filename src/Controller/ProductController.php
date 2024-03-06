<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use App\Entity\User;


class ProductController extends AbstractController
{


    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $pr,SessionInterface $s,UserRepository $repo): Response
    {
        $iduser=$s->get('name');

        $user=$repo->findoneby(['name'=>$iduser])
        ;
        return $this->render('product/index.html.twig', [
            'products' => $pr->findAll(),
            'user'=> $user,

        ]);
    }

}
