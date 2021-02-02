<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
    	$em = $this->getDoctrine()->getManager();
    	$articles = $em->getRepository(Article::class)->find3Last();
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
	        "articles" => $articles
        ]);
    }

	/**
	 * @Route("/Article/{id}", name="learticle")
	 */
	public function article($id): Response
	{
		$em = $this->getDoctrine()->getManager();
		$articles = $em->getRepository(Article::class)->find($id);
		return $this->render('accueil/Learticle.html.twig', [
			'controller_name' => 'AccueilController',
			"article" => $articles
		]);
	}

	/**
	 * @Route("/TousLesArticles", name="Allarticles")
	 */
	public function art(): Response
	{
		$em = $this->getDoctrine()->getManager();
		$articles = $em->getRepository(Article::class)->findall();
		return $this->render('accueil/article.html.twig', [
			'controller_name' => 'AccueilController',
			"articles" => $articles
		]);
	}
}
