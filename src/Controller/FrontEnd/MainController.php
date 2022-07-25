<?php

namespace App\Controller\FrontEnd;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    #[Route('/', name: "home")]
    public function index(ArticleRepository $articleRepo): Response
    {
        $articles = $articleRepo->findAll();

        return $this->render('Home/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
