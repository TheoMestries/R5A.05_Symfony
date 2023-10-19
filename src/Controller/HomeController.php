<?php

namespace App\Controller;

use App\Entity\Response;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/reports/{status}', name: 'app_home', defaults: ["status" => "all"])]
    public function index(ArticleRepository $articleRepository, string $status = "all"): HttpResponse
    {
        switch ($status) {
            case 'publié':
                $scp_reports = $articleRepository->findBy(['status' => 'publié']);
                break;
            case 'brouillon':
                $scp_reports = $articleRepository->findBy(['status' => 'brouillon']);
                break;
            case 'published_with_response':
                $scp_reports = $articleRepository->findPublishedWithAtLeastOneResponse();
                break;
            default:
                $scp_reports = $articleRepository->findAll();
        }


        return $this->render('home/index.html.twig', [
            'scp_reports' => $scp_reports,
            'status' => $status
        ]);

    }




}
