<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    #[Route('/news', name: 'news')]
    public function index(Request $request, NewsRepository $newsRepository, PaginatorInterface $paginator): Response
    {
        $query = $newsRepository->findAll();
        $news = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit', 10)/*limit per page*/
        );
        return $this->render('news/index.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/news/{id}', name: 'show')]
    public function show($id, NewsRepository $newsRepository): Response
    {

        return $this->render('news/show.html.twig', [
            'new' => $newsRepository->find($id),
        ]);
    }

    #[Route('/news/{id}/delete', name: 'news-delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function destroy($id, NewsRepository $newsRepository, EntityManagerInterface $manager){

        $news = $newsRepository->find($id);

        $manager->remove($news);
        $manager->flush();
        return new RedirectResponse($this->urlGenerator->generate('news'));

    }
}
