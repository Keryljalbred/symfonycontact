<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

#[Route('/admin')]

class productController extends AbstractController

{
    public function __construct(private ProductRepository
   $productRepository, private RequestStack $requestStack, 
   private EntityManagerInterface $entityManager)
   {

   }
   #[Route ('/product',name:'admin.product.index')]
    public function index(): Response
    {
        //dd ($this->productRepository->findAll());
        return $this ->render('admin/product/index.html.twig' , [
            'products' => $this->productRepository->findAll(),
        ]
    );
    }
    #[Route ('/product/form',name:'admin.product.form')]
    #[Route ('/product/form/update{id}',name:'admin.product.update')]
    public function form(int $id = null): Response
    {
        //creation formulaire 

        $entity = $id? $this -> productRepository -> find($id) : new Product();
        $type = ProductType::class;
        //conserver le nom de l'image du produit au cas ou il y'a pas de selection
        //d'image lors de la modification
        $entity->prevImage = $entity->getImage();

        //dd($entity);

        $form = $this ->createForm($type, $entity);
        //dd ($type);
        //dd ($this->productRepository->findAll());

        //recuperer la saisie dans la requete http
        $form -> handleRequest($this->requestStack->getMainRequest());

        //si le formulaire est valide et soumis
        if ($form-> isSubmitted() && $form ->isValid()) {
            // gestion de l'image
            $filename = ByteString::fromRandom(32) ->lower();

            //dd($entity);
            $file = $entity -> getImage();
            if ($file instanceof UploadedFile) {
                $fileExtension = $file-> guessClientExtension();

                $file-> move('img',"$filename.$fileExtension");
                $entity-> setImage("$filename.$fileExtension");
                //supprimer l'ancienne image
                if ($id) unlink("img/{$entity -> prevImage}"); 
                    # code...
                }
            
            //si image non selectionnee
                else {
                // recuperer la valeur de l'ancien image
                $entity -> setImage( $entity->prevImage);
            }
            //dd($filename, $entity);
            $this -> entityManager -> persist($entity);
            $this -> entityManager -> flush();
            $message = $id ? 'Product Updated' : 'Product created';
            $this -> addFlash('notice', $message);
            //redirection vers la page d'acceuil de l'admin
            return $this -> redirectToRoute('admin.product.index');
        }
        return $this ->render('admin/product/form.html.twig',[ 
            'form' =>$form -> createView(),
        //, [
            //'products' => $this->productRepository->findAll(),
        //]
    ]);
    }
    #[Route('/product/delete/{id}', name: 'admin.product.delete')]
    public function delete (int $id):RedirectResponse {
        //selectionner l'entite a supprimer
        $entity = $this -> productRepository->find($id);
        //supprimer l'entite
        $this-> entityManager->remove($entity);
        $this-> entityManager->flush();

        //supprimer l'image
        unlink("img/{$entity -> getImage()}");

        //message de confirmation
        $this->addFlash('notice', 'Product deleted');
        //redirection
        return $this -> redirectToRoute('admin.product.index');
    }
    // public function index(): Response
    // {
    //     return $this->render('admin/product/index.html.twig', [
    //         'controller_name' => 'productController',
    //     ]);
    // }
}
