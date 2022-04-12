<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    public function __construct(
        Connection $connection,
        private UserRepositoryInterface $userRepository,
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
    ) {
        parent::__construct($connection);
    }

    /**
     * @throws CommentNotFoundException
     */
    public function findById(int $id): Comment
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

        $comment =  new Comment(
            author: $this->userRepository->findById($result->author_id),
            article: $this->articleRepository->findById($result->article_id),
            text: $result->text,
        );

        $comment->setId($result->id);
        return $comment;
    }

    public function isExists(int $id): bool
    {
        try {
            $this->findById($id);
        } catch (CommentNotFoundException) {
            return false;
        }

        return true;
    }
}
