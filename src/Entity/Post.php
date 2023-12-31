<?php
namespace App\Entity;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'liked')]
    #[ORM\JoinTable('Likes')]
    private Collection $likedBy;

    #[ORM\OneToMany(mappedBy: 'post',
     targetEntity: PostPics::class, orphanRemoval: true,cascade:['persist','remove'])]
    private Collection $pics;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $alertExpiration = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Notification::class,cascade:['remove'])]
    private Collection $notifications;


  
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->created=new DateTime();
        $this->likedBy = new ArrayCollection();
        $this->pics = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
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

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

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

    /**
     * @return Collection<int, User>
     */
    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function addLikedBy(User $likedBy): static
    {
        if (!$this->likedBy->contains($likedBy)) {
            $this->likedBy->add($likedBy);
        }

        return $this;
    }

    public function removeLikedBy(User $likedBy): static
    {
        $this->likedBy->removeElement($likedBy);

        return $this;
    }

    /**
     * @return Collection<int, PostPics>
     */
    public function getPics(): Collection
    {
        return $this->pics;
    }

    public function addPic(PostPics $pic): static
    {
        if (!$this->pics->contains($pic)) {
            $this->pics->add($pic);
            $pic->setPost($this);
        }

        return $this;
    }

    public function removePic(PostPics $pic): static
    {
        if ($this->pics->removeElement($pic)) {
            // set the owning side to null (unless already changed)
            if ($pic->getPost() === $this) {
                $pic->setPost(null);
            }
        }

        return $this;
    }

    public function getAlertExpiration(): ?\DateTimeInterface
    {
        return $this->alertExpiration;
    }

    public function setAlertExpiration(?\DateTimeInterface $alertExpiration): static
    {
        $this->alertExpiration = $alertExpiration;

        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setPost($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getPost() === $this) {
                $notification->setPost(null);
            }
        }

        return $this;
    }
    
}
