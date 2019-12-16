<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * Add an Category
     *
     * @Route("/add", name="category_add")
     * @param Request $request
     * @return Response
     */

    public function addCategory(Request $request):Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/_formcategory.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Add an Category
     *
     * @Route("/", name="category_index")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function indexCategory(CategoryRepository $categoryRepository):Response
    {
        return $this->render(
            'category/index_category.html.twig',
            [
                'categories' => $categoryRepository->findAll(),
            ]
        );
    }
}