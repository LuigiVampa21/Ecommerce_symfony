<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{

    private SluggerInterface $slugger;
    public function __constructor(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }



    public function load(ObjectManager $manager): void
    {
        $parent = new Categories();
        $parent->setName('Computing');
        $parent->setSlug($this->slugger->slug($parent->getName()));
        $manager->persist($parent);
        
        
        $category = new Categories();
        $category->setName('Computer');
        $category->setSlug('computer');
        $category->setParent($parent);
        $manager->persist($category);

        $manager->flush();
    }
}
