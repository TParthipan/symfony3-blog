<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @UniqueEntity(fields="name", message="Il existe déjà une catégorie de ce nom")
 */
class Category {

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=20, unique=true)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(max="20",maxMessage="Le nom ne doit pas depasser {{ limit }} caratères",min="2",minMessage="Le nom  doit avoir au minimum {{ limit }} caratères")
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category")
     */
    private $articles;

    public function __construct() {
        //une collection vide pour les articles
        $this->articles = new ArrayCollection();
    }

    /**
     * Get id
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     * @param string $name
     * @return Category
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticles() {
        return $this->articles;
    }

    /**
     * @param ArrayCollection $articles
     * @return ArrayCollection
     */
    public function setArticles(ArrayCollection $articles) {
        $this->articles = $articles;
        return $this;
    }

    /**
     * 
     * @return int le nb d'article de la catégorie
     */
    public function countArticles() {
        return $this->articles->count();
    }
    /**
     * 
     * @return bool la categorie contient-elle des articles ? 
     */
    public function hasArticles() {
        return $this->countArticles() != 0;
    }

}
