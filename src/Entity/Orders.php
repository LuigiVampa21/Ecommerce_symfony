<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use App\Entity\Traits\CreatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 */
class Orders
{

    use CreatedAtTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    #[ORM\Column(type: "string", length: 20, unique: true)]
    private $reference;


    /**
     * @ORM\ManyToOne(targetEntity=Tags::class, inversedBy="orders")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "orders")]
    #[ORM\JoinColumn(nullable: false)]
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=OrdersDetails::class, mappedBy="orders", orphanRemoval=true)
     */
    private $ordersDetails;

    public function __construct()
    {
        $this->ordersDetails = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }


    public function getTags(): ?Tags
    {
        return $this->tags;
    }

    public function setTags(?Tags $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, OrdersDetails>
     */
    public function getOrdersDetails(): Collection
    {
        return $this->ordersDetails;
    }

    public function addOrdersDetail(OrdersDetails $ordersDetail): self
    {
        if (!$this->ordersDetails->contains($ordersDetail)) {
            $this->ordersDetails[] = $ordersDetail;
            $ordersDetail->setOrders($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrdersDetails $ordersDetail): self
    {
        if ($this->ordersDetails->removeElement($ordersDetail)) {
            // set the owning side to null (unless already changed)
            if ($ordersDetail->getOrders() === $this) {
                $ordersDetail->setOrders(null);
            }
        }

        return $this;
    }
}
