<?php 

namespace App\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    #[Route('/', name: "home")]
    public function index(): Response
    {
        $data = [
            'fName' => 'Dylan',
            'lName' => 'LH',
            'age' => 27
        ];
        return $this->render('Home/index.html.twig', [
            'data' => $data
        ]);
    }
}