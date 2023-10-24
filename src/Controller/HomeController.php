<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/{status}', name: 'app_home', defaults: ["status" => "publié"])]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request, string $status = "all"): HttpResponse
    {
        $queryBuilder = $articleRepository->createQueryBuilder('a');

        switch ($status) {
            case 'publié':
                $queryBuilder->where('a.status = :status')->setParameter('status', 'publié');
                break;
            case 'brouillon':
                $queryBuilder->where('a.status = :status')->setParameter('status', 'brouillon');
                break;
            case 'published_with_response':
                $articleRepository->findPublishedWithAtLeastOneResponse($queryBuilder);
                break;
            default:
                // No additional conditions for "all"
                break;
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2  // 2 articles per page
        );

        $pageNumber = $request->query->getInt('page', 1);

        $realPageNumber = $pageNumber ;

        if ($pageNumber <3) {
            $pageNumber = 3;
        }

        $pageMaxNumber = $pageNumber + 2 ;



        if ($pageMaxNumber > $pagination->getPageCount()) {
            $pageMaxNumber = $pagination->getPageCount();
        }


        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'status' => $status,
            'page_number' => $pageNumber,
            'page_max_number' => $pageMaxNumber,
            'real_page_number' => $realPageNumber,
        ]);
    }
}
