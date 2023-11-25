<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;


class ContactController extends AbstractController

{
    
    public function __construct(private ContactRepository $contactRepository,
     private RequestStack $requestStack,
    private EntityManagerInterface $entityManager)
    {
        
    }
    #[Route ('/contact' , name:'contact.index')]
    public function index():Response
    {
        return $this ->render('contact/index.html.twig' , [
            'contacts' => $this ->contactRepository -> findAll(),
        ]);
    }
    #[Route('contact/form', name:'contact.form')]
    #[Route ('/contact/form/update{id}',name:'contact.update')]
    public function form(int $id = null): Response
    {
        $entity = $id? $this -> contactRepository -> find($id) : new Contact();
        $type = ContactType::class;
        $form = $this ->createForm($type,$entity);
        $form -> handleRequest($this->requestStack->getMainRequest());

        //si le formulaire est valide et soumis
        if ($form-> isSubmitted() && $form ->isValid()) {
            $this -> entityManager -> persist($entity);
            $this -> entityManager -> flush();
            $message = $id ? 'Contact Updated' :'Contact created';
            $this -> addFlash('notice', $message);
            //redirection vers la page d'acceuil de l'admin
            return $this -> redirectToRoute('contact.index');
        }
        return $this ->render('contact/form.html.twig',[ 
            'form' =>$form -> createView(),
        //, [
            //'products' => $this->productRepository->findAll(),
        //]
    ]);
        }
        #[Route('/contact/delete/{id}', name: 'contact.delete')]
    public function delete (int $id):RedirectResponse {
        //selectionner l'entite a supprimer
        $entity = $this -> contactRepository->find($id);
        //supprimer l'entite
        $this-> entityManager->remove($entity);
        $this-> entityManager->flush();

        //message de confirmation
        $this->addFlash('notice', 'Contact deleted');
        //redirection
        return $this -> redirectToRoute('contact.index');
    }
    
}
