<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="date", length=11, nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $countryAndCity;

    /**
     * @ORM\Column(type="string")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $additionalInformations;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $verificationCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param mixed $profilePicture
     */
    public function setProfilePicture($profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param mixed $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return mixed
     */
    public function getCountryAndCity()
    {
        return $this->countryAndCity;
    }

    /**
     * @param mixed $countryAndCity
     */
    public function setCountryAndCity($countryAndCity): void
    {
        $this->countryAndCity = $countryAndCity;
    }

    /**
     * @return mixed
     */
    public function getAdditionalInformations()
    {
        return $this->additionalInformations;
    }

    /**
     * @param mixed $additionalInformations
     */
    public function setAdditionalInformations($additionalInformations): void
    {
        $this->additionalInformations = $additionalInformations;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * @param mixed $verificationCode
     */
    public function setVerificationCode($verificationCode): void
    {
        $this->verificationCode = $verificationCode;
    }

    /**
     * @ORM\PrePersist
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
            $dateAndTime = new DateTime();
            $this->createdAt = $dateAndTime->format('Y-m-d H:i:s');
    }
}
