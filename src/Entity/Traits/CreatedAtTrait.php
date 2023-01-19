<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;


trait CreatedAtTrait
{
        /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[ORM\Column(type: "datetime_immutable", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $created_at;

    

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

}


?>