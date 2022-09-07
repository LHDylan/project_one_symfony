<?php

namespace App\Controller\BackEnd;

use App\Data\SearchData;
use App\Entity\Article;
use App\Entity\Comments;
use App\Form\ArticleType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin')]
class AdminController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $emi,
        private ArticleRepository $repoArticle,
        private CommentsRepository $commentsRepo,
    ) {
    }

    #[Route('/', name: 'admin')]
    public function dashboard()
    {
        return $this->render('Backend/index.html.twig',);
    }

    #[Route('/article', name: 'admin.article')]
    public function adminListArticle(ArticleRepository $articleRepo, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);

        $articles = $articleRepo->findSearch($data, false);

        return $this->renderForm('BackEnd/Article/index.html.twig', [
            'articles' => $articles,
            'form' => $form
        ]);
    }

    #[Route('/article/new', name: 'admin.article.new')]
    public function createArticle(Request $request, Security $security): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUser($security->getUser());
            $article->setActive(true);
            $this->emi->persist($article);
            $this->emi->flush();
            $this->addFlash('success', 'Article created successfully');
            return $this->redirectToRoute('admin');
        }

        return $this->render('BackEnd/Article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/modify/{id}-{slug}', name: 'admin.article.edit')]
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

        return $this->render('BackEnd/Article/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/article/switch/{id}", name: "admin.article.switch", methods: ['GET'])]
    public function switchVisibilityArticles(int $id)
    {
        $article = $this->repoArticle->find($id);

        if ($article) {
            $article->isActive() ? $article->setActive(false) : $article->setActive(true);
            $this->repoArticle->add($article, true);

            return new Response('Visibility changed', 201);
        }

        return new Response('Comment not found', 404);
    }

    #[Route('/article/delete/{id}', name: 'admin.article.delete', methods: 'DELETE|POST')]
    public function deleteArticle(int $id, Article $article, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))) {
            $this->emi->remove($article);
            $this->emi->flush();
            $this->addFlash('success', 'Article deleted successfully.');

            return $this->redirectToRoute('admin');
        }

        $this->addFlash('error', 'Token expired.');
        return $this->redirectToRoute('admin');
    }

    #[Route("/articles/{id}-{slug}/comments", name: "admin.article.comments")]
    public function adminComments(int $id, string $slug)
    {
        $comments = $this->commentsRepo->findByArticle($id, $slug);

        if (!$comments) {
            $this->addFlash('error', 'no comments found');
            return $this->redirectToRoute('admin');
        }

        return $this->render('BackEnd/article/comments.html.twig', [
            'comments' => $comments
        ]);
    }

    #[Route("/comments/switch/{id}", name: "admin.comments.switch", methods: ['GET'])]
    public function switchVisibilityComment(int $id)
    {
        $comment = $this->commentsRepo->find($id);

        if ($comment) {
            $comment->isActive() ? $comment->setActive(false) : $comment->setActive(true);
            $this->emi->persist($comment);
            $this->emi->flush();

            return new Response('Visibility changed', 201);
        }

        return new Response('Comment not found', 404);
    }
}
