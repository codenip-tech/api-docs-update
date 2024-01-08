<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
readonly class Product
{
    public function __construct(
        #[ORM\Id]
        #[ORM\CustomIdGenerator]
        #[ORM\Column]
        private string $id,
        #[ORM\Column(type: 'string', length: 100)]
        private string $name,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
