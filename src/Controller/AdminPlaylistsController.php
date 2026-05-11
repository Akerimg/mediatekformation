<?php
namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPlaylistsController extends AbstractController {

    private const ADMIN_PLAYLISTS_TEMPLATE = "pages/admin/playlists.html.twig";
    private const ADMIN_PLAYLIST_TEMPLATE = "pages/admin/playlist.html.twig";

    private $playlistRepository;
    private $categorieRepository;
    private $formationRepository;

    public function __construct(PlaylistRepository $playlistRepository,
                                CategorieRepository $categorieRepository,
                                FormationRepository $formationRepository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }

    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response {
        if ($champ === 'nbFormations') {
            $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
        } else {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_PLAYLISTS_TEMPLATE, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/admin/playlists/supprimer/{id}', name: 'admin.playlists.supprimer', methods: ['POST'])]
    public function supprimer($id, Request $request): Response {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException("Playlist introuvable.");
        }
        if (count($playlist->getFormations()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette playlist car elle contient des formations.');
            return $this->redirectToRoute('admin.playlists');
        }
        if ($this->isCsrfTokenValid('supprimer' . $id, $request->request->get('_token'))) {
            $this->playlistRepository->remove($playlist);
        }
        return $this->redirectToRoute('admin.playlists');
    }

    #[Route('/admin/playlists/nouveau', name: 'admin.playlists.nouveau')]
    public function nouveau(Request $request): Response {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render(self::ADMIN_PLAYLIST_TEMPLATE, [
            'playlist' => $playlist,
            'formplaylist' => $form->createView(),
            'playlistformations' => []
        ]);
    }

    #[Route('/admin/playlists/modifier/{id}', name: 'admin.playlists.modifier')]
    public function modifier($id, Request $request): Response {
        $playlist = $this->playlistRepository->find($id);
        if (!$playlist) {
            throw $this->createNotFoundException("Playlist introuvable.");
        }
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        $playlistformations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::ADMIN_PLAYLIST_TEMPLATE, [
            'playlist' => $playlist,
            'formplaylist' => $form->createView(),
            'playlistformations' => $playlistformations
        ]);
    }
}
