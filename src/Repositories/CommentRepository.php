<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Entities\User\User;
use App\Commands\GetCommand;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Factories\EntityManagerFactory;
use App\Exceptions\CommentNotFoundException;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getComment($statement);
    }

    /**
     * @throws CommentNotFoundException
     */
    private function getComment(PDOStatement $statement): Comment
    {
        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            throw new CommentNotFoundException('Comment not found');
        }

        /**
         * @var EntityManagerFactoryInterface $entityMangerFactory
         */
        $entityMangerFactory = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityMangerFactory->getRepository(User::class));
        $author = $command->handle($result->author_id);
        $command = new GetCommand($entityMangerFactory->getRepository(Article::class));
        $article = $command->handle($result->article_id);
        $comment = new Comment($author, $article, $result->text);
        $comment->setId($result->id);
        return $comment;
    }
}
