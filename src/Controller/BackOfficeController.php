<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Produit;
use App\Entity\Realisation;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Form\ArticleType;
use App\Form\CategorieType;
use App\Form\ProduitType;
use App\Form\RealisationType;
use App\Form\ReportRdvType;
use App\Notification\MailNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;


class BackOfficeController extends AbstractController
{
    /**
     * @Route("/back/office", name="back_office")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
    	/*Last Rdv*/
	    $em = $this->getDoctrine()->getManager();
	    $lastRdv = $em->getRepository(Reservation::class)->findLast();
	    $lastId = $em->getRepository(Reservation::class)->findIdlast();
	    if (!empty($lastId)){
		    $lastId = $lastId[0]['id'];
	    }
	    $rea= $em->getRepository(Realisation::class)->findAll();
	    $prod= $em->getRepository(Produit::class)->findAll();
	    $art= $em->getRepository(Article::class)->findAll();
	    $ach= $em->getRepository(Achat::class)->findLast();
	    $client = "";
	    if(!empty($lastRdv)){
		    $client = $em->getRepository(Utilisateur::class)->find($lastRdv["0"]["un_utilisateur_id"]);
	    }

        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
	        "lastRdv"=>$lastRdv,
	        'client'=>$client,
	        "rea"=>$rea,
	        'produit' =>$prod,
	        'article' =>$art,
	        "achat"=>$ach,
	        'lastId'=>$lastId
        ]);
    }


///////////////////////////////////////////////
/// Gestion boutique
///////////////////////////////////////////////
	/**
	 * @Route("/back/office/AjoutProduit", name="back_office_ajout_produit")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function product(Request $request): Response
	{
		$Product = new Produit();
		$form = $this->createForm(ProduitType::class,$Product);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$Product= $form->getData();
			$Product = $Product->setPrix($form->get('prix')->getData()*1.014+0.25);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($Product);
			$entityManager->flush();
			return $this->redirectToRoute('back_office',[]);
		}

		$categ = new Category();
		$formC = $this->createForm(CategorieType::class,$categ);
		$error=[];

		$formC->handleRequest($request);
		if ($formC->isSubmitted() && $formC->isValid()){
				$categ->setLibelle($formC->get("libelle")->getData());
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($categ);
				$entityManager->flush();
		}

		return $this->render('back_office/product.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView(),
			'formC' => $formC->createView(),
			"error"=>$error
		]);
	}

	/**
	 * @Route("/back/office/DelProduit/{id}", name="back_office_produitDel")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function productD($id)
	{
		$em = $this->getDoctrine()->getManager();
		$Product = $em->getRepository(Produit::class)->find($id);
		$achats = $em->getRepository(Achat::class)->findByProduct($Product->getId());

			if (empty($achats)){
				$em->remove($Product);
				$em->flush();
				return $this->redirectToRoute('back_office',[]);

			}
			else {
				return $this->redirectToRoute('back_office',[]);
			}
		return $this->redirectToRoute('back_office',[]);
	}

    /**
     * @Route("/back/office/TousLesProduits", name="back_office_allP")
     * @IsGranted("ROLE_ADMIN")
     */
    public function productAll()
    {
        $em = $this->getDoctrine()->getManager();
        $Product = $em->getRepository(Produit::class)->findAll();
        return $this->render('back_office/lesProduits.html.twig', [
            'controller_name' => 'BackOfficeController',
            'produits' => $Product,
        ]);
    }

	/**
	 * @Route("/back/office/ModProduit/{id}", name="back_office_ajout_produitMod")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function productM(Request $request,$id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Product = $em->getRepository(Produit::class)->find($id);
		$form = $this->createForm(ProduitType::class,$Product);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$Product= $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($Product);
			$entityManager->flush();
			return $this->redirectToRoute('back_office',[]);
		}
		return $this->render('back_office/Modproduct.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/back/office/gestionAchat", name="back_office_gestion_achat")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function gestionAchat(Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$AllAchat = $em->getRepository(Achat::class)->findAll();

		return $this->render('back_office/gestionAchat.html.twig', [
			'controller_name' => 'BackOfficeController',
			'achats' => $AllAchat
		]);
	}

	/**
	 * @Route("/back/office/archives", name="back_office_gestion_archive")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function archive(Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$AllAchat = $em->getRepository(Achat::class)->findAll();

		return $this->render('back_office/archive.html.twig', [
			'controller_name' => 'BackOfficeController',
			'achats' => $AllAchat
		]);
	}


	/**
	 * @Route("/back/office/donner/{id}", name="back_office_donner")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function donner($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());

		$ach->setRecu("recu");

		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Votre produit est en votre possession  ! ",$user->getMail(),"Merci de votre achat..","Détail de l'achat: ".$ach->getDescriptionAchat());




		return $this->redirectToRoute('back_office_gestion_achat',[]);
	}

	/**
	 * @Route("/back/office/livrer/{id}", name="back_office_livrer")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function livré($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());

		$ach->setRecu("livrer");

		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Votre produit a étais expédier  ! ",$user->getMail(),"Il ne vous reste plus qu'attendre votre produit.","Détail de l'achat: ".$ach->getDescriptionAchat());



		return $this->redirectToRoute('back_office_gestion_achat',[]);
	}

	/**
	 * @Route("/back/office/produit/salon/{id}", name="back_office_salon")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function salon($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$ach = $em->getRepository(Achat::class)->find($id);
		$user = $em->getRepository(Utilisateur::class)->find($ach->getLeUtilisateur());
		$ach->setRecu("salon");
		$em->persist($ach);
		$em->flush();
		$mail->notifyContact("Récupèrer votre produit au salon ! ",$user->getMail(),"","Détail de l'achat: ".$ach->getDescriptionAchat());

		return $this->redirectToRoute('back_office_gestion_achat',[]);
	}


	///////////////////////////////////////////////
	/// Fin Gestion boutique
	///////////////////////////////////////////////



	///////////////////////////////////////////////
	/// Gestion Article
	///////////////////////////////////////////////

	/**
	 * @Route("/back/office/AjoutArticle", name="back_office_ajout_article")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function article(Request $request, MailNotification $mail): Response
	{
		$art = new Article();
		$form = $this->createForm(ArticleType::class,$art);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$art = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($art);
			$entityManager->flush();
			$utilisateur =$entityManager->getRepository(Utilisateur::class)->findAll();
			foreach ($utilisateur as $user){
				$mail->notifyContact("Nouvel article ! ",$user->getMail(),"Un nouvel article à été ajouter !","Détail de l'article: ".$art->getDescription());
			}

			return $this->redirectToRoute('back_office',[]);
		}
		return $this->render('back_office/article.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView(),
		]);

	}

	/**
	 * @Route("/back/office/DelArticle/{id}", name="back_office_ajout_articleDel")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function artD($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$art = $em->getRepository(Article::class)->find($id);
		$em->remove($art);
		$em->flush();

		return $this->redirectToRoute('back_office',[]);
	}

	/**
	 * @Route("/back/office/ModArticle/{id}", name="back_office_ajout_articleMod")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function artM(Request $request,$id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$art = $em->getRepository(Article::class)->find($id);
		$form = $this->createForm(ArticleType::class,$art);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$art= $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($art);
			$entityManager->flush();
			return $this->redirectToRoute('back_office',[]);
		}
		return $this->render('back_office/ModArticle.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView(),
		]);
	}

	///////////////////////////////////////////////
	/// Fin Gestion Article
	///////////////////////////////////////////////



	///////////////////////////////////////////////
	///  Gestion reservation
	///////////////////////////////////////////////

	/**
	 * @Route("/back/office/gestionReservation", name="back_office_gestion")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function gestionReservation(Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$AllRdv = $em->getRepository(Reservation::class)->findAll();

		return $this->render('back_office/gestionR.html.twig', [
			'controller_name' => 'BackOfficeController',
			'rdvs' => $AllRdv,
		]);
	}

	/**
	 * @Route("/back/office/del/{id}", name="back_office_SUP")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function suprimme_a_admin($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);
		$Rdv->setEtat('annuler');
		$mail->notify("rendez-vous refusez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été refusez, vous pouvez faire une autre demande ! :");
		$em->remove($Rdv);
		$em->flush();


		return $this->redirectToRoute('back_office',[]);
	}

	/**
	 * @Route("/back/office/rdv/del/{id}", name="back_office_delete")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function suprimme_in_gestion($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);
		$Rdv->setEtat('annuler');
		$mail->notify("rendez-vous refusez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été refusez, vous pouvez faire une autre demande ! :");
		$em->remove($Rdv);
		$em->flush();


		return $this->redirectToRoute('back_office_gestion',[]);
	}

	/**
	 * @Route("/back/office/rdv/decaler/{id}", name="back_office_modify")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function reporterRdv($id, MailNotification $mail,Request $request): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);
		$form = $this->createForm(ReportRdvType::class,$Rdv);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$Rdv= $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($Rdv);
			$entityManager->flush();
			$mail->notify("rendez-vous modifier ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été modier, vous pouvez vous la nouvelle date de rendez vous ! :");
			return $this->redirectToRoute('back_office_gestion',[]);
		}


		return $this->render('back_office/reporterRdv.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView()
		]);
	}


	/**
	 * @Route("/back/office/rdv/annuler/{id}", name="back_office_annulers")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function Annulers($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);
		$Rdv->setEtat('annuler');
		$mail->notify("rendez-vous refusez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été refusez, vous pouvez faire une autre demande ! :");
		$em->persist($Rdv);
		$em->flush();


		return $this->redirectToRoute('back_office_gestion',[]);
	}

	/**
	 * @Route("/back/office/rdv/valider/{id}", name="back_office_valider")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function Valider($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);

		$Rdv->setEtat('valider');

		$mail->notify("rendez-vous acceptez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été acceptez il ne vous reste plus qu'a aller au rendez-vous le :");

		$em->persist($Rdv);
		$em->flush();

		return $this->redirectToRoute('back_office_gestion',[]);
	}




	/**
	 * @Route("/back/office/annuler/{id}", name="back_office_AA")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function Annuler_a_Administration($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);
		$Rdv->setEtat('annuler');
		$mail->notify("rendez-vous refusez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été refusez, vous pouvez faire une autre demande ! :");
		$em->persist($Rdv);
		$em->flush();


		return $this->redirectToRoute('back_office',[]);
	}

	/**
	 * @Route("/back/office/valider/{id}", name="back_office_VV")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function valider_a_Administration($id, MailNotification $mail): Response
	{
		$em = $this->getDoctrine()->getManager();
		$Rdv = $em->getRepository(Reservation::class)->find($id);

		$Rdv->setEtat('valider');

		$mail->notify("rendez-vous acceptez ! ",$Rdv->getUnUtilisateur()->getMail(),$Rdv->getDateReservation(),$Rdv->getMessage(),"Votre demande de rendez-vous a été acceptez il ne vous reste plus qu'a aller au rendez-vous le :");

		$em->persist($Rdv);
		$em->flush();

		return $this->redirectToRoute('back_office',[]);
	}


	///////////////////////////////////////////////
	/// Fin  Gestion reservation
	///////////////////////////////////////////////



	///////////////////////////////////////////////
	/// Gestion photo
	///////////////////////////////////////////////
	/**
	 * @Route("/back/office/photo", name="back_office_gestionPhoto")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function photo(Request $request): Response
	{
		$realisation = new Realisation();
		$form = $this->createForm(RealisationType::class,$realisation);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()){
			$realisation= $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($realisation);
			$entityManager->flush();
			return $this->redirectToRoute('back_office',[]);
		}

		return $this->render('back_office/rea.html.twig', [
			'controller_name' => 'BackOfficeController',
			'form' => $form->createView()
		]);

	}

	/**
	 * @Route("/back/office/photo/Del/{id}", name="back_office_gestionPhotoDel")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function photoDel($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$realisation = $em->getRepository(Realisation::class)->find($id);
		$emt = $this->getDoctrine()->getManager();
		$emt->remove($realisation);
		$emt->flush();

		return $this->redirectToRoute('back_office',[]);

	}
	///////////////////////////////////////////////
	/// Fin Gestion photo
	///////////////////////////////////////////////
}
