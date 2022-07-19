<?php

namespace App\Controller\BackEnd;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $emi;

    private $repoArticle;

    public function __construct(EntityManagerInterface $emi, ArticleRepository $repoArticle)
    {
        $this->emi = $emi;
        $this->repoArticle = $repoArticle;
    }

    #[Route('/article', name: 'admin')]
    public function adminListArticle()
    {
        $articles = $this->repoArticle->findAll();

        return $this->render('Backend/Article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/new', name: 'admin.article.new')]
    public function createArticle(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emi->persist($article);
            $this->emi->flush();
            $this->addFlash('success', 'Article created successfully');
            return $this->redirectToRoute('admin');
        }

        return $this->render('BackEnd/Article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/modify/{id}-{slug}', name: 'admin.article.update')]
    public function modifyArticle(int $id, string $slug, Request $request): Response
    {
        $article = $this->repoArticle->find($id);

        if (!$article) {
            $this->addFlash('error', 'Article not found');
            return $this->redirectToRoute('admin');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emi->persist($article);
            $this->emi->flush();
            $this->addFlash('success', 'Article modified successfully');
            return $this->redirectToRoute('admin');
        }

        return $this->render('BackEnd/Article/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
