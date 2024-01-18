<?php

namespace App\Entity;

use App\Repository\DepartamentoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepartamentoRepository::class)
 */
class Departamento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Dependencia::class, inversedBy="departamentos")
     */
    private $dependencia;

    /**
     * @ORM\OneToMany(targetEntity=Identificador::class, mappedBy="departamento")
     */
    private $identificadors;

    /**
     * @ORM\OneToMany(targetEntity=Usuario::class, mappedBy="departamento")
     */
    private $usuarios;

    public function __construct()
    {
        $this->identificadors = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    public function __toString()
    {
        $dep = ($this->dependencia) ? $this->dependencia->getCentro() : '';

        return $this->nombre.' - '.$dep;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDependencia(): ?Dependencia
    {
        return $this->dependencia;
    }

    public function setDependencia(?Dependencia $dependencia): self
    {
        $this->dependencia = $dependencia;

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setDepartamento($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getDepartamento() === $this) {
                $usuario->setDepartamento(null);
            }
        }

        return $this;
    }
}
