<?php

namespace App\Controller\FrontEnd;

use App\Data\SearchData;
use App\Entity\Article;
use App\Entity\Comments;
use App\Form\CommentsType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article.index')]
    public function index(ArticleRepository $articleRepo, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);

        $articles = $articleRepo->findSearch($data, true);

        return $this->renderForm('Article/index.html.twig', [
            'articles' => $articles,
            'form' => $form
        ]);
    }

    #[Route('/details/{slug}', name: 'article.show')]
    public function show(
        ?Article $article,
        Request $request,
        Security $security,
        EntityManagerInterface $emi,
        CommentsRepository $commentsRepo
    ): Response {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article not found.');

            return $this->redirectToRoute('home');
        }

        $comments = $commentsRepo->findActiveByArticle($article->getId());

        $comment = new Comments();

        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($security->getUser())
                ->setArticle($article)
                ->setActive(true);

            $emi->persist($comment);
            $emi->flush();

            $this->addFlash('success', 'comment has been posted successfully');
            return $this->redirectToRoute('article.show', [
                'id' => $article->getId(),
                'slug' => $article->getSlug()
            ], 301);
        }

        return $this->renderForm('article/show.html.twig', [
            'article' => $article,
            'form' => $form,
            'comments' => $comments
        ]);
    }
}
