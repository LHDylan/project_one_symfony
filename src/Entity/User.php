<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Vich\Uploadable]
#[UniqueEntity(
    fields: ['email'],
    message: 'This email is already used by another user.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Regex(
        pattern: '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',
        message: 'Veuillez rentrer un email valide.'
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^((?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9]).{6,})\S$/',
        message: 'Password must be at least 6 characters, and contains at least one uppercase letter, one lowercase letter, one number and can`t contains spaces.'
    )]
    private ?string $password = null;

    #[ORM\Column(length: 101)]
    #[Assert\Length(
        min: 2,
        minMessage: 'First name must be at least {{ limit }} characters long',
        max: 101,
        maxMessage: 'First name can\'t exceed {{ limit }} characters long',
    )]
    private ?string $fName = null;

    #[ORM\Column(length: 101)]
    #[Assert\Length(
        min: 2,
        minMessage: 'First name must be at least {{ limit }} characters long',
        max: 101,
        maxMessage: 'First name can\'t exceed {{ limit }} characters long',
    )]
    private ?string $lName = null;

    #[ORM\Column(length: 150)]
    #[Assert\Length(
        max: 150,
        maxMessage: 'First name can\'t exceed {{ limit }} characters long',
    )]
    private ?string $address = null;

    #[ORM\Column]
    #[Assert\Regex(
        pattern: '/^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$/',
        message: 'Veuillez rentrer un code postal valide.'
    )]
    private ?string $zipCode = null;

    #[ORM\Column(length: 101)]
    #[Assert\Length(
        max: 101,
        maxMessage: 'First name can\'t exceed {{ limit }} characters long',
    )]
    private ?string $city = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Article::class)]
    private Collection $articles;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     */
    #[Vich\UploadableField(mapping: 'users_image', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $ImageSize = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $imageUpdatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comments::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->roles,
            'password' => $this->password,
            'fName' => $this->fName,
            'lName' => $this->lName,
            'address' => $this->address,
            'zipCode' => $this->zipCode,
            'city' => $this->city,
            'image_name' => $this->imageName
        ];
    }

    public function __unserialize(array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->roles = $data['roles'];
        $this->password = $data['password'];
        $this->fName = $data['fName'];
        $this->lName = $data['lName'];
        $this->address = $data['address'];
        $this->zipCode = $data['zipCode'];
        $this->city = $data['city'];
        $this->imageName = $data['image_name'];
    }

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
    public function getUserIdentifier(): string
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFName(): ?string
    {
        return $this->fName;
    }

    public function setFName(string $fName): self
    {
        $this->fName = $fName;

        return $this;
    }

    public function getLName(): ?string
    {
        return $this->lName;
    }

    public function setLName(string $lName): self
    {
        $this->lName = $lName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->imageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->ImageSize;
    }

    public function setImageSize(?int $ImageSize): self
    {
        $this->ImageSize = $ImageSize;

        return $this;
    }

    public function getImageUpdatedAt(): ?\DateTimeInterface
    {
        return $this->imageUpdatedAt;
    }

    public function setImageUpdatedAt(?\DateTimeInterface $imageUpdatedAt): self
    {
        $this->imageUpdatedAt = $imageUpdatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
