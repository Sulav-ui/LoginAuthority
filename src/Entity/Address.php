<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AddressRepository::class)
 */
class Address
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="address")
     */
    private $houseplace;

    public function __construct()
    {
        $this->houseplace = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getHouseplace(): Collection
    {
        return $this->houseplace;
    }

    public function addHouseplace(User $houseplace): self
    {
        if (!$this->houseplace->contains($houseplace)) {
            $this->houseplace[] = $houseplace;
            $houseplace->setAddress($this);
        }

        return $this;
    }

    public function removeHouseplace(User $houseplace): self
    {
        if ($this->houseplace->removeElement($houseplace)) {
            // set the owning side to null (unless already changed)
            if ($houseplace->getAddress() === $this) {
                $houseplace->setAddress(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->houseplace;
    }

    public function setHouseplace(string $houseplace): self
    {
        $this->houseplace = $houseplace;

        return $this;
    }
}
