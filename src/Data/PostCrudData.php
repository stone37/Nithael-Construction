<?php

namespace App\Data;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class PostCrudData implements CrudDataInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 180)]
    public ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $content = null;

    public ?User $author = null;

    public ?Category $category = null;

    public ?bool $online = true;

    public ?UploadedFile $file = null;

    public ?Post $entity; 

    #[Pure] public static function makeFromPost(Post $post): self
    {
        $data = new self();
        $data->title = $post->getTitle();
        $data->content = $post->getContent();
        $data->category = $post->getCategory();
        $data->author = $post->getAuthor();
        $data->online = $post->isOnline();
        $data->entity = $post;

        return $data;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return PostType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setTitle($this->title)
            ->setContent($this->content)
            ->setCategory($this->category)
            ->setAuthor($this->author)
            ->setOnline($this->online)
            ->setFile($this->file);
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): PostCrudData
    {
        $this->author = $author;

        return $this;
    }
}

