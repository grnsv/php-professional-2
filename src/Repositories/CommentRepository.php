<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use App\Entities\User\User;
use App\Commands\GetCommand;
use Psr\Log\LoggerInterface;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Factories\EntityManagerFactory;
use App\Exceptions\CommentNotFoundException;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    public function __construct(
        Connection $connection,
        private LoggerInterface $logger,
    ) {
        parent::__construct($connection);
    }

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
            $this->logger->error('Comment not found');
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

    public function isExists(int $id): bool
    {
        try {
            $this->get($id);
        } catch (CommentNotFoundException) {
            return false;
        }

        return true;
    }
}
