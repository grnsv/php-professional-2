<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Commands\GetCommand;
use App\Entities\Article\Article;
use App\Entities\EntityInterface;
use App\Factories\EntityManagerFactory;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * @param EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var Article $entity
         */
        $statement =  $this->connector->getConnection()
            ->prepare("INSERT INTO articles (author_id, title, text) 
                VALUES (:author_id, :title, :text)");

        $statement->execute(
            [
                ':author_id' => $entity->getAuthor()->getId(),
                ':title' => $entity->getTitle(),
                ':text' => $entity->getText(),
            ]
        );
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function get(int $id): Article
    {
        $statement = $this->connector->getConnection()->prepare(
            'SELECT * FROM articles WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getArticle($statement, $id);
    }

    /**
     * @throws ArticleNotFoundException
     */
    private function getArticle(PDOStatement $statement, int $articleId): Article
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new ArticleNotFoundException(
                sprintf("Cannot find article with id: %s", $articleId)
            );
        }

        /**
         * @var EntityManagerFactoryInterface $entityManager
         */
        $entityManager = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityManager->getRepository('user'));
        $author = $command->handle($result['author_id']);
        $article = new Article($author, $result['title'], $result['text']);
        $article->setId($articleId);
        return $article;
    }
}
