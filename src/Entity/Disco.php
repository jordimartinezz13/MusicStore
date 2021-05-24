<?php

namespace App\Entity;

use App\Repository\DiscoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DiscoRepository::class)
 */
class Disco
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
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\Positive
     */
    private $precio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @ORM\ManyToOne(targetEntity=Artista::class, inversedBy="discos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $artista;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getArtista(): ?Artista
    {
        return $this->artista;
    }

    public function setArtista(?Artista $artista): self
    {
        $this->artista = $artista;

        return $this;
    }
}
