<?php

namespace App\Controller;

use App\Entity\BlogArticle;
use App\Repository\BlogArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[Route('/api/blog-articles')]
class BlogArticleController extends AbstractController
{
    const excludedWords = ["de", "la", "le", "l", "les"];

    private $entityManager;
    private $blogArticleRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, BlogArticleRepository $blogArticleRepository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->blogArticleRepository = $blogArticleRepository;
        $this->validator = $validator;
    }

    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: BlogArticle::class)
    )]
    #[Route('', name: 'blog_articles_create', methods: ['POST'])]
    public function create(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $article = new BlogArticle();
        $article->setTitle($request->get('title'));
        $article->setContent($request->get('content'));
        $keywords = $this->findTopWords($request->get('content'), self::excludedWords);
        $article->setKeywords($keywords);
        $article->setStatus($request->get('status'));
        $article->setPublicationDate(new \DateTime($request->get('publicationDate')));
        $article->setCreationDate(new \DateTime());
        $article->setSlug($slugger->slug($request->get('title')));
        $article->setAuthor($this->getUser());

        // File upload 
        $file = $request->files->get('coverPictureRef');
        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move('./uploaded_pictures', $filename);
            $article->setCoverPictureRef($filename);
        }

        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Blog article created!'], Response::HTTP_CREATED);
    }

    #[Route('', name: 'blog_articles_list', methods: ['GET'])]
    public function list(SerializerInterface $serializer): JsonResponse
    {
        $articles = $this->blogArticleRepository->findAll();
        // Serialize the articles
        $jsonArticles = $serializer->serialize($articles, 'json', ['groups' => ['list']]);

        return new JsonResponse($jsonArticles, 200, [], true);
    }

    #[Route('/{id}', name: 'blog_articles_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $article = $this->blogArticleRepository->find($id);

        if (!$article) {
            throw new NotFoundHttpException('Blog article not found');
        }

        return $this->json($article, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'blog_articles_update', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $article = $this->blogArticleRepository->find($id);

        if (!$article) {
            throw new NotFoundHttpException('Blog article not found');
        }

        // Update fields
        // if (($request->get('title'))) {
            $article->setTitle($request->get('title'));
        // }
        // if (($request->get('content'))) {
            $article->setContent($request->get('content'));
        // }
        // if (($request->get('status'))) {
            $article->setStatus($request->get('status'));
        // }
        // if (($request->get('keywords'))) {
            $article->setKeywords($request->get('keywords'));
        // }

        // update cover picture
        $file = $request->files->get('coverPictureRef');
        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move('./uploaded_pictures', $filename);
            $article->setCoverPictureRef($filename);
        }

        // Validate and save
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Blog article updated!'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'blog_articles_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $article = $this->blogArticleRepository->find($id);

        if (!$article) {
            throw new NotFoundHttpException('Blog article not found');
        }

        $article->setStatus('deleted');
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Blog article soft deleted!'], Response::HTTP_OK);
    }

    function findTopWords(string $text, array $banned): array {
        // Convert text to lowercase and remove non-alphabetical characters
        $text = strtolower($text);
        $text = preg_replace('/[^a-z\s]/', ' ', $text);
    
        // Split text into words
        $words = array_filter(explode(' ', $text), fn($word) => strlen($word) > 0);
    
        // Create a frequency map
        $frequency = [];
        foreach ($words as $word) {
            if (!in_array($word, $banned)) {
                if (isset($frequency[$word])) {
                    $frequency[$word]++;
                } else {
                    $frequency[$word] = 1;
                }
            }
        }
    
        // Sort the words by frequency in descending order
        arsort($frequency);
    
        // Return the top 3 most frequent words
        return array_slice(array_keys($frequency), 0, 3);
    }
    
}
