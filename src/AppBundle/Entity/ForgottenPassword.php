<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ForgottenPassword {

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column()
     * @var string 
     */
    private $token;

    public function __construct() {
        $this->generateToken();
    }

    /**
     * 
     * @return \AppBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * 
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @return ForgottenPassword
     */
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @param string $token
     * @return ForgottenPassword
     */
    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function generateToken() {
        $this->setToken(hash('sha512', uniqid()));
    }

}
