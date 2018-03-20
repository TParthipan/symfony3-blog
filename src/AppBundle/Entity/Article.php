<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 */
class Article {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var Category 
     * @ORM\ManyToOne(targetEntity="Category",fetch="EAGER", inversedBy="articles")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var User 
     * @ORM\ManyToOne(targetEntity="User",fetch="EAGER")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article", cascade={"remove"})
     */
    private $comments;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Image(mimeTypesMessage="Le fichier n'est pas une image")
     */
    private $image;

    public function __construct() {
        //une collection vide 
        $this->comments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Article
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Article
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setCategory(Category $category) {
        $this->category = $category;
        return $this;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getAuthorFullname() {
        if (!is_null($this->author)) {
            return $this->author->getFullname();
        }
    }

    /**
     * 
     * @return ArrayCollection
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * 
     * @param \AppBundle\Entity\ArrayCollection $comments
     * @return ArrayCollection
     */
    public function setComments(ArrayCollection $comments) {
        $this->comments = $comments;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * 
     * @param string $image
     * @return Article
     */
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function hasImage() {
        return !is_null($this->image);
    }

}
