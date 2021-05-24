<?php

namespace App\Entity;

use App\Repository\ArtistaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArtistaRepository::class)
 */
class Artista
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Campo no puede estar vacío")
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @ORM\OneToMany(targetEntity=Disco::class, mappedBy="artista")
     */
    private $discos;

    public function __construct()
    {
        $this->discos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function getNombreCOMPLETO(): ?string
    {
        return $this->nombre . " " . $this->apellidos;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * @return Collection|Disco[]
     */
    public function getDiscos(): Collection
    {
        return $this->discos;
    }

    public function addDisco(Disco $disco): self
    {
        if (!$this->discos->contains($disco)) {
            $this->discos[] = $disco;
            $disco->setArtista($this);
        }

        return $this;
    }

    public function removeDisco(Disco $disco): self
    {
        if ($this->discos->removeElement($disco)) {
            // set the owning side to null (unless already changed)
            if ($disco->getArtista() === $this) {
                $disco->setArtista(null);
            }
        }

        return $this;
    }
}
