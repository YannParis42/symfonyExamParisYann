<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository,
private EntityManagerInterface $em, private PaginatorInterface $paginator)
{
}
    #[Route('/', name: 'app_home')]
    // public function index(): Response
    public function findByIsActive(Request $request): Response
        {
            $qb = $this->productRepository->getProductByDate();

            $pagination = $this->paginator->paginate(
                $qb, /* LA QUERY */
                $request->query->getInt('page', 1), /* LE NUMERO DE PAGE */
                9 /* NOMBRE DELEMENTS PAR PAGE */
            );
            
        // $productEntities = $this->productRepository->findBy(['isActive'=> 1]);
        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    // #[Route('/home', name: 'app_active_product')]
    // public function findByIsActive(): Response
    //     {
    //     $productEntities = $this->productRepository->findBy(['isActive'=> 1]);
    //     return $this->render('home/index.html.twig', [
    //         'products'=>$productEntities
    //     ]);
    // }
}
