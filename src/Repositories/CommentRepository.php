<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Commands\GetCommand;
use App\Entities\Comment\Comment;
use App\Entities\EntityInterface;
use App\Factories\EntityManagerFactory;
use App\Exceptions\CommentNotFoundException;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    /**
     * @param EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        /**
         * @var Comment $entity
         */
        $statement =  $this->connector->getConnection()
            ->prepare("INSERT INTO comments (author_id, article_id, text) 
                VALUES (:author_id, :article_id, :text)");

        $statement->execute(
            [
                ':author_id' => $entity->getAuthor()->getId(),
                ':article_id' => $entity->getArticle()->getId(),
                ':text' => $entity->getText(),
            ]
        );
    }

    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        $statement = $this->connector->getConnection()->prepare(
            'SELECT * FROM comments WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getComment($statement, $id);
    }

    /**
     * @throws CommentNotFoundException
     */
    private function getComment(PDOStatement $statement, int $commentId): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            throw new CommentNotFoundException(
                sprintf("Cannot find comment with id: %s", $commentId)
            );
        }

        /**
         * @var EntityManagerFactoryInterface $entityManager
         */
        $entityManager = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityManager->getRepository('user'));
        $author = $command->handle($result['author_id']);
        $command = new GetCommand($entityManager->getRepository('article'));
        $article = $command->handle($result['article_id']);
        $comment = new Comment($author, $article, $result['text']);
        $comment->setId($commentId);
        return $comment;
    }
}
