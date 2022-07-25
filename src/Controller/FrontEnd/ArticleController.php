<?php

namespace App\Controller\FrontEnd;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/{id}-{slug}', name: 'article.show')]
    public function index(int $id, string $slug, ArticleRepository $articleRepo): Response
    {
        $article = $articleRepo->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$article) {
            $this->addFlash('error', 'Article not found.');

            return $this->redirectToRoute('home');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article
        ]);
    }
}
