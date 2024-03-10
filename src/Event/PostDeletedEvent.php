<?php

namespace App\Event;

use App\Entity\Post;

class PostDeletedEvent
{
    public function __construct(private Post $post)
    {
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}