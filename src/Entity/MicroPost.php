<?php

namespace App\Entity;

use App\Repository\MicroPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MicroPostRepository::class)]
class MicroPost
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    // Ниже строчка - проверка на то что форма не пустая
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5,max: 255,minMessage: 'Слишком короткое название')]
    private ?string $title = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5,max: 500,minMessage: "Too short message")]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    // Свойство orphanRemoval true означает что когда каждый пост будет удален комментарии будут удалены вместе с постом
    // cascade persist позволяет создать пост и комментарий в одно и то же время
    // fetch:Eager отображает в массиве все комментарии в свойстве комментс, в случае если стоит LAZY не отображает
    #[ORM\OneToMany(mappedBy: 'microPost', targetEntity: Comment::class, orphanRemoval: true,cascade: ['persist'],fetch: 'EAGER')]
    // По скольку свойство комментс является коллекцией он наследует в себя разные методы например count
    // Смотреть в Collection -> Implementations -> AbstractLazyCollection
    private Collection $comments;


    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'liked')]
    private Collection $likedBy;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->likedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    //
    public function addComment(Comment $comment): self
    {
        // Если текущий комментарий еще не в списке наших комментариев, то добавляет его
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            // И добавляет ссылку на наш пост в свойство класса Коммент
            $comment->setMicroPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMicroPost() === $this) {
                $comment->setMicroPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function addLikedBy(User $likedBy): self
    {
        if (!$this->likedBy->contains($likedBy)) {
            $this->likedBy->add($likedBy);
        }

        return $this;
    }

    public function removeLikedBy(User $likedBy): self
    {
        $this->likedBy->removeElement($likedBy);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
