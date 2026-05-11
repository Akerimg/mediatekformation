<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur du back office pour la gestion des categories.
 * Permet de lister, ajouter et supprimer des categories.
 * L'ajout n'est possible que si le nom n'existe pas deja.
 * La suppression n'est possible que si la categorie n'est rattachee a aucune formation.
 *
 * @author Kerim
 */
class AdminCategoriesController extends AbstractController {

    /**
     * Chemin du template de la liste des categories en back office.
     */
    private const ADMIN_CATEGORIES_TEMPLATE = "pages/admin/categories.html.twig";

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Constructeur, injection du repository.
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des catégories avec le formulaire d'ajout.
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response {
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_CATEGORIES_TEMPLATE, [
            'categories' => $categories
        ]);
    }

    /**
     * Ajoute une nouvelle catégorie après vérification de l'unicité du nom.
     * @param Request $request La requête HTTP contenant le formulaire
     * @return Response Redirection vers la liste des catégories
     */
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

    /**
     * Supprime une catégorie si aucune formation ne lui est rattachée.
     * @param int $id Identifiant de la catégorie à supprimer
     * @param Request $request La requête HTTP avec le token CSRF
     * @return Response Redirection vers la liste des catégories
     */
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
