<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;
    
    #[ORM\Column]
    private ?bool $is_read = null;
    
    #[ORM\ManyToOne(inversedBy: 'sent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;
    
    #[ORM\ManyToOne(inversedBy: 'recived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;
    
    public function __construct()
    {
        $this->is_read=0;
        $this->title='a';
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

    public function isIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): static
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
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
}
