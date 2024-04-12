<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ClasseRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Classe;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\Query\ResultSetMapping;


class ClasseController extends AbstractController
{
   

    #[Route('/classestest', name: 'app_classe' ,methods:'GET')]
    public function indextest(SerializerInterface $serializer ,ClasseRepository $classerepository): JsonResponse
    {
        $classes = $classerepository->findAll();
        return $this->json($classes);

        /* $classes = $classerepository->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::GROUPS => ['produit'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true); */

         /*  $categories = $produitrepo->findAll();
        $produits = $categorierepo->findAll();
        $data = [
            'categories' => $categories,
            'produits' => $produits,
        ];
        return $this->json($data, 200, [], ['groups' => ['categorie', 'produit']]); */
    }

    #[Route('/classes', name: 'app_classe' ,methods:'GET')]
    public function index(SerializerInterface $serializer ,ClasseRepository $classerepository): JsonResponse
    {
        $classes = $classerepository->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $circularReferenceHandler,
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true);
        


        /* $classes = $classerepository->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::GROUPS => ['produit'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true); */

         /*  $categories = $produitrepo->findAll();
        $produits = $categorierepo->findAll();
        $data = [
            'categories' => $categories,
            'produits' => $produits,
        ];
        return $this->json($data, 200, [], ['groups' => ['categorie', 'produit']]); */

    }


    #[Route('/classe/{id}', name: 'app_classe_show', methods: ['GET'])]
    public function show($id, SerializerInterface $serializer, ClasseRepository $classerepository): Response
    {
        $classes = $classerepository->find($id);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $circularReferenceHandler,
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/classecreate', name: 'app_classe_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $newClasse = new Classe();
        $newClasse->setNom($data['nom']);
        $newClasse->setNbretudiants($data['nbreetudiant']);
        $entityManager->persist($newClasse);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($newClasse, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/classe/{id}', name: 'app_classe_update', methods: ['PUT'])]
    public function update($id , Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $classeRepository = $entityManager->getRepository(Classe::class);
        $existingClasse = $classeRepository->find($id);
        if (!$existingClasse) {
            return new JsonResponse(['message' => 'Classe not found'], 404);
        }
        $existingClasse->setNom($data['nom']);
        $existingClasse->setNbretudiants($data['nbreetudiant']);
        $entityManager->persist($existingClasse);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($existingClasse, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/classe/{id}', name: 'deleteclasse', methods: ['DELETE'])]
    public function delete( $id, EntityManagerInterface $em,ClasseRepository $classeRepository): JsonResponse
    {
        $classe = $classeRepository->find($id);
        $em->remove($classe);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    
}
