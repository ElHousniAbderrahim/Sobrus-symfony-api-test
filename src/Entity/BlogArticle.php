<?php

namespace App\Entity;

use App\Repository\BlogArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlogArticleRepository::class)]
class BlogArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['list'])]
    private $author;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['list'])]
    private $title;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Type("\DateTimeInterface")]
    #[Groups(['list'])]
    private $publicationDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Type("\DateTimeInterface")]
    #[Groups(['list'])]
    private $creationDate;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['list'])]
    private $content;

    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank]
    #[Groups(['list'])]
    private $keywords = [];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['list'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['list'])]
    private $coverPictureRef;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['draft', 'published', 'deleted'], message: 'Invalid status')]
    #[Groups(['list'])]
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCoverPictureRef(): ?string
    {
        return $this->coverPictureRef;
    }

    public function setCoverPictureRef(string $coverPictureRef): static
    {
        $this->coverPictureRef = $coverPictureRef;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
