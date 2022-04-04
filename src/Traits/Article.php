<?php

namespace App\Traits;

use App\Entities\Article\Article as ArticleEntity;

trait Article
{
    private ArticleEntity $article;

    public function getArticle(): ArticleEntity
    {
        return $this->article;
    }

    public function setArticle(ArticleEntity $article): self
    {
        $this->article = $article;

        return $this;
    }
}
