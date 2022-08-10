<?php

namespace App\Entity;

use App\Repository\DisciplineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DisciplineRepository::class)
 */
class Discipline
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $datastartAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $dataendAt;

    /**
     * @ORM\ManyToOne(targetEntity=Teacher::class, inversedBy="disciplines")
     */
    private $teacher;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDatastartAt(): ?\DateTimeImmutable
    {
        return $this->datastartAt;
    }

    public function setDatastartAt(?\DateTimeImmutable $datastartAt): self
    {
        $this->datastartAt = $datastartAt;

        return $this;
    }

    public function getDataendAt(): ?\DateTimeImmutable
    {
        return $this->dataendAt;
    }

    public function setDataendAt(?\DateTimeImmutable $dataendAt): self
    {
        $this->dataendAt = $dataendAt;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }
}
