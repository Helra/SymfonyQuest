<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/wild/show/{slug}", methods={"GET"}, requirements={"slug"="[a-z0-9.-]*"}, name="wild_show")
     */

    public function show(string $slug): Response
    {
        $slugClean = ucwords(str_replace("-", " ", $slug));
        return $this->render('wild/show.html.twig', ['slug' => $slugClean]);
    }
}
