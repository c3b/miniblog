<?php

namespace Emds\MiniblogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Emds\MiniblogBundle\Entity\Message;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // récupération des messages de la bdd avant l'appel du template
        $messages = $this->getDoctrine()
                         ->getEntityManager()
                         ->getRepository('EmdsMiniblogBundle:Message')
                         ->findAll();
        return $this->render('EmdsMiniblogBundle:Default:index.html.twig',
        array('messages' => $messages));
    }
    
    public function afficherAction($id)
    {
        //affichage de chaque message dans la bonne page
        $message = $this->getDoctrine()
                         ->getEntityManager()
                         ->getRepository('EmdsMiniblogBundle:Message')
                         ->find($id);
        return $this->render('EmdsMiniblogBundle:Default:afficher.html.twig', array('message' => $message));
    }
    
     private function gestionFormulaire($message, $twig)
    {
        //Création du constructeur du formulaire
        $formBuilder = $this->createFormBuilder($message);
        
        //ajout des champs pour la construciton du formulaire
        $formBuilder->add('date', 'date')
                    ->add('titre', 'text')
                    ->add('contenu', 'textarea')
                    ->add('auteur', 'text');
        //generation du formualaire
        $form = $formBuilder->getForm();
        
        //recuperation de la requete
        $request = $this->getRequest();
        //Controle de la methode d'appel de la page
        if($request->getMethod() == "POST"){
            //hydratation de l'objet
            $form->handleRequest($request);
            
            //Controle de la validité du formulaire
            if($form->isValid()){
                //Creation de l'entity manager
                $em = $this->getDoctrine()->getEntityManager();
                //Enregistrement des données
                $em->persist($message);
                $em->flush();
                //redirection vers la liste des messages
                return $this->redirect($this->generateUrl('EmdsMiniblogBundle_homepage'));
            }
        }
        //envoi du formulaire au template
        return $this->render('EmdsMiniblogBundle:Default:'.$twig.'.html.twig', array('form' => $form->createView()));
    }
        
        
    
    public function ajouterAction()
    {
        //Creation de l'objet qui va etre lié au formulaire
        $message = new Message();
        
        //gestion complete du formulaire et de son retour
        return $this->gestionFormulaire($message, 'ajouter');
    }
    
    public function modifierAction($id)
    {
        
        //Creation de l'objet qui va etre lié au formulaire
        $message = $this->getDoctrine()->getEntityManager()
                        ->getRepository('EmdsMiniblogBundle:Message')
                        ->find($id);
        
        //Gestion complete du formulaire et de son retour
        return $this->gestionFormulaire($message, 'modifier');
     }
        
     
    
    
    public function supprimerAction($id)
    {
        //Création de l'entity manager
        $em = $this->getDoctrine()->getEntityManager();
        
        //Récupération des messages de la bdd
        $message = $em->getRepository('EmdsMiniblogBundle:Message')->find($id);
        //Suppression du message
        $em->remove($message);
        $em->flush();
        
        //redirection vers la liste des messages
        return $this->redirect($this->generateUrl('EmdsMiniblogBundle_homepage'));
    }
    
    
}