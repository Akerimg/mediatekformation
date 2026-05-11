<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoriesController extends AbstractController {

    private const ADMIN_CATEGORIES_TEMPLATE = "pages/admin/categories.html.twig";

    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response {
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_CATEGORIES_TEMPLATE, [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/ajouter', name: 'admin.categories.ajouter', methods: ['POST'])]
    public function ajouter(Request $request): Response {
        $name = trim($request->request->get('name', ''));
        if (!$this->isCsrfTokenValid('ajouter_categorie', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin.categories');
        }
        if ($name === '') {
            $this->addFlash('error', 'Le nom de la catégorie est obligatoire.');
            return $this->redirectToRoute('admin.categories');
        }
        if ($this->categorieRepository->findOneByName($name) !== null) {
            $this->addFlash('error', 'Cette catégorie existe déjà.');
            return $this->redirectToRoute('admin.categories');
        }
        $categorie = new Categorie();
        $categorie->setName($name);
        $this->categorieRepository->add($categorie);
        return $this->redirectToRoute('admin.categories');
    }

    #[Route('/admin/categories/supprimer/{id}', name: 'admin.categories.supprimer', methods: ['POST'])]
    public function supprimer($id, Request $request): Response {
        $categorie = $this->categorieRepository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException("Catégorie introuvable.");
        }
        if (count($categorie->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle est rattachée à des formations.');
            return $this->redirectToRoute('admin.categories');
        }
        if ($this->isCsrfTokenValid('supprimer' . $id, $request->request->get('_token'))) {
            $this->categorieRepository->remove($categorie);
        }
        return $this->redirectToRoute('admin.categories');
    }
}
