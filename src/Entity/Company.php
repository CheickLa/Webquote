<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\DBAL\Types\LegalStatusType;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 14)]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'LegalStatusType')]
    private ?LegalStatusType $legal_status = null;

    #[ORM\Column(length: 255)]
    private ?string $sector = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $phone_number = null;

    #[ORM\Column]
    private ?float $sales = null;

    #[ORM\Column]
    private ?bool $role = null;

    #[ORM\OneToMany(mappedBy: 'company_id', targetEntity: ServiceCategory::class, orphanRemoval: true)]
    private Collection $serviceCategories;

    #[ORM\OneToMany(mappedBy: 'client_id', targetEntity: Quote::class, orphanRemoval: true)]
    private Collection $quotes;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function __construct()
    {
        $this->serviceCategories = new ArrayCollection();
        $this->quotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLegalStatus(): ?LegalStatusType
    {
        return $this->legal_status;
    }

    public function setLegalStatus(LegalStatusType $legal_status): static
    {
        $this->legal_status = $legal_status;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(string $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getSales(): ?float
    {
        return $this->sales;
    }

    public function setSales(float $sales): static
    {
        $this->sales = $sales;

        return $this;
    }

    public function getRole(): ?bool
    {
        return $this->role;
    }

    public function setRole(bool $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    public function getServiceCategories(): Collection
    {
        return $this->serviceCategories;
    }

    public function addServiceCategory(ServiceCategory $serviceCategory): static
    {
        if (!$this->serviceCategories->contains($serviceCategory)) {
            $this->serviceCategories->add($serviceCategory);
            $serviceCategory->setCompanyId($this);
        }

        return $this;
    }

    public function removeServiceCategory(ServiceCategory $serviceCategory): static
    {
        if ($this->serviceCategories->removeElement($serviceCategory)) {
            // set the owning side to null (unless already changed)
            if ($serviceCategory->getCompanyId() === $this) {
                $serviceCategory->setCompanyId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quote>
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): static
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes->add($quote);
            $quote->setClientId($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): static
    {
        if ($this->quotes->removeElement($quote)) {
            // set the owning side to null (unless already changed)
            if ($quote->getClientId() === $this) {
                $quote->setClientId(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
