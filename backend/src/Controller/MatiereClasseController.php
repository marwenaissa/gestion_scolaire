<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ProduitRepository;
use App\Repository\MagazinRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;
use App\Entity\Magazin;
use App\Entity\Categorie;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\Persistence\ManagerRegistry;

class MatiereClasseController extends AbstractController
{
    #[Route('/matiere/classe', name: 'app_matiere_classe')]
    public function index(): Response
    {
        return $this->render('matiere_classe/index.html.twig', [
            'controller_name' => 'MatiereClasseController',
        ]);
    }


    #[Route('/store', name: 'store')]
    public function store( ProduitRepository $produitrepo ,  EntityManagerInterface $entityManager): JsonResponse
    {
        /* $entityManager = $this->doctrine->getManager();
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Produit::class, 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'nomProduit', 'nom');
        $rsm->addFieldResult('p', 'descriptionProduit', 'description');
        // Ajoutez d'autres champs si nécessaire
        $sql = "
            SELECT p.id, p.nom_produit, p.description_produit, m.id as magazin_id, m.nom as magazin_nom, m.dimension as magazin_dimension
            FROM produit p
            INNER JOIN magazin_produit mp ON p.id = mp.produit_id
            INNER JOIN magazin m ON mp.magazin_id = m.id
        ";
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $produits = $query->getResult(); */ 


        $matieres = $matiererepository->findAll();
        $data = [];
        foreach ($matieres as $matiere) {
            $classes = $matiere->getclasses();
            foreach ($classes as $classe) {
                $data[] = [
                    'id_classe' => $classe->getId(),
                    'nom_classe' => $classe->getNom(),
                    'id_matiere' => $matiere->getId(),
                    'nom_matiere' => $matiere->getNom()
                ];
            }
        }
        $normalizer = new ObjectNormalizer(null,null,null,null,null,null,
            [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
            ]
        );
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($data, 'json');
        return new JsonResponse($json, 200, [], true);   
    }


    #[Route('/creatematiereclasse', name: 'update_matiere_classe')]
    public function createAssociation(Request $request ,  ProduitRepository $produitrepo , MagazinRepository $magazinrepo ,  EntityManagerInterface $entityManager ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $produitId = $data['produitId'];
        $magazinId = $data['magazinId'];
        $produit = $produitrepo->find($produitId);
        $magazin = $magazinrepo->find($magazinId);
        if (!$produit || !$magazin) {
            return new JsonResponse(['error' => 'Produit ou magasin non trouvé'], 404);
        }
        $produit->addMagazin($magazin);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Association créée avec succès'], 200);
    }


    #[Route('/updatematiereclasse/{idpr}/{idmag}', name: 'update_matiere_classe')]
    public function updateAssociation($idpr , $idmag ,Request $request ,  ProduitRepository $produitrepo , MagazinRepository $magazinrepo ,  EntityManagerInterface $entityManager ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $produitId = $data['produitId'];
        $magazinId = $data['magazinId'];
        $magazin = $magazinrepo->find($magazinId);
        $produit = $produitrepo->find($produitId);
        $magazins = $produit->getMagazins();
        foreach ($magazins as $mag) {
            if ($mag->getId() === $magazin->getId()) {
                return new JsonResponse(['message' => 'Le magasin fait partie des magasins du produit.'], 200);
            }
        }
        if (!$produit || !$magazin) {
            return new JsonResponse(['error' => 'Produit ou magasin non trouvé'], 404);
        }
        $sql = "UPDATE produit_magazin 
        SET produit_id = :newProduitId
        WHERE produit_id = :produitId
        AND magazin_id = :magazinId";
        $query = $entityManager->getConnection()->prepare($sql);
        $query->bindValue('newProduitId', $produitId);
        $query->bindValue('produitId', $idpr);
        $query->bindValue('magazinId', $idmag); // Assuming you have the old magazin_id
        $query->execute();
        return new JsonResponse(['message' => 'Association créée avec succès'], 200);
    }

    #[Route('/matiereclasse/{id}', name: 'deletematiereclasse', methods: ['DELETE'])]
    public function delete( $id, EntityManagerInterface $em, ProfesseurRepository $professeurprofesseur): JsonResponse
    {
        $professeur = $professeurprofesseur->find($id);
        $em->remove($professeur);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    

}


