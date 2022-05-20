<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
public function __construct(private ProductRepository $productRepository,
private EntityManagerInterface $em, private PaginatorInterface $paginator)
{
}

    #[Route('/product', name: 'app_product')]
    public function index(Request $request): Response
    { 
        $qb = $this->productRepository->getQbAll();

        $pagination = $this->paginator->paginate(
            $qb, /* LA QUERY */
            $request->query->getInt('page', 1), /* LE NUMERO DE PAGE */
            9 /* NOMBRE DELEMENTS PAR PAGE */
        );

        $product = $this->productRepository->findAll();
    
        return $this->render('product/index.html.twig', [
            'products' => $product,
            'pagination'=>$pagination,
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

    #[Route('/product/user', name: 'app_user_product')]
    public function findByUser(): Response
        {
        $user = $this->getUser();
        $productEntities = $this->productRepository->findBy(['createdBy'=> $user]);
        return $this->render('product/user.html.twig', [
            'products'=>$productEntities
        ]);
    }

    #[Route('/product/{id}', name: 'app_one_product')]
    public function findOne($id): Response
        {
            $productEntity = $this->productRepository->find($id);
    
            return $this->render('product/productDetail.html.twig', [
              'product'=>$productEntity
            ]);
        }

    // ajouter un produit
    #[Route('/ajouter', name: 'app_product_add')]
    public function add(Request $request): Response
    {
        $user = $this->getUser();
        $productEntity = new Product();
        $productEntity->setCreatedAt(new DateTime());
        $productEntity->setIsActive(true);
        $productEntity->setCreatedBy($user);
        $form = $this->createForm(ProductType::class, $productEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // modifier un produit
    #[Route('/modifier/{id}', name: 'app_product_modify')]
    public function modify(string $id, Request $request): Response
    {
        $productEntity = $this->productRepository->find($id);
        $form = $this->createForm(ProductType::class, $productEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/modifier.html.twig', [
    
            'form' => $form->createView(),
            'product' => $productEntity
        ]);
    }

       // désactiver un produit
       #[Route('/desactivate/{id}', name: 'app_product_desactivate')]
       public function desactivate(string $id, Request $request): Response
       {
        $productEntity = $this->productRepository->find($id);
        $productEntity->setIsActive(false);
        $this->em->persist($productEntity);
        $this->em->flush();
        return $this->redirectToRoute('app_home');
         
       }

    // supprimer un produit
    #[Route('/supprimer/{id}', name: 'app_product_delete')]
    public function delete(int $id): Response
    {
        $productEntity = $this->productRepository->find($id);
        $this->em->remove($productEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_product');
    }



}
