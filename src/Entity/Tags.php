<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagsRepository::class)
 */
class Tags
{

    use CreatedAtTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    #[ORM\Column(type: "string", length: 10, uniquer: true)]
    private $code;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $discount;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_usage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $validity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_valid;



    /**
     * @ORM\ManyToOne(targetEntity=TagsTypes::class, inversedBy="tags")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: TagsTypes::class, inversedBy: "tags")]
    #[ORM\JoinColumn(nullable: false)]
    private $tags_types;

    /**
     * @ORM\OneToMany(targetEntity=Orders::class, mappedBy="tags")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getMaxUsage(): ?int
    {
        return $this->max_usage;
    }

    public function setMaxUsage(int $max_usage): self
    {
        $this->max_usage = $max_usage;

        return $this;
    }

    public function getValidity(): ?\DateTimeInterface
    {
        return $this->validity;
    }

    public function setValidity(\DateTimeInterface $validity): self
    {
        $this->validity = $validity;

        return $this;
    }

    public function isIsValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setIsValid(bool $is_valid): self
    {
        $this->is_valid = $is_valid;

        return $this;
    }


    public function getTagsTypes(): ?TagsTypes
    {
        return $this->tags_types;
    }

    public function setTagsTypes(?TagsTypes $tags_types): self
    {
        $this->tags_types = $tags_types;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setTags($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getTags() === $this) {
                $order->setTags(null);
            }
        }

        return $this;
    }
}
