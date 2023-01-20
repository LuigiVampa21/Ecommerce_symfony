<?php

namespace App\DataFixtures;

use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{

    private SluggerInterface $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }



    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Computing', null,$manager);
        
        $this->createCategory('Laptop', $parent,$manager); 
        $this->createCategory('Screen', $parent,$manager);
        
        $parent = $this->createCategory('Clothing', null,$manager);
        
        $this->createCategory('Men', $parent,$manager); 
        $this->createCategory('Women', $parent,$manager);
        $this->createCategory('Children', $parent,$manager);

        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        return $category;
    }
}
