<?php

namespace App\Entity;

use App\Repository\ModificationHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModificationHistoryRepository::class)]
class ModificationHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $modificationDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentBefore = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contentAfter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }

    public function setModificationDate(\DateTimeInterface $modificationDate): static
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    public function getContentBefore(): ?string
    {
        return $this->contentBefore;
    }

    public function setContentBefore(string $contentBefore): static
    {
        $this->contentBefore = $contentBefore;

        return $this;
    }

    public function getContentAfter(): ?string
    {
        return $this->contentAfter;
    }

    public function setContentAfter(string $contentAfter): static
    {
        $this->contentAfter = $contentAfter;

        return $this;
    }
}
