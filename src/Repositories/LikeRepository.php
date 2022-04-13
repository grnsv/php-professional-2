<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use App\Entities\Like\Like;
use Psr\Log\LoggerInterface;
use App\Exceptions\LikeNotFoundException;

class LikeRepository extends EntityRepository implements LikeRepositoryInterface
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
     * @throws LikeNotFoundException
     */
    public function findById(int $id): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getLike($statement);
    }

    /**
     * @throws LikeNotFoundException
     */
    private function getLike(PDOStatement $statement): Like
    {
        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            $this->logger->error('Like not found');
            throw new LikeNotFoundException('Like not found');
        }

        $like =  new Like(
            user: $this->userRepository->findById($result->user_id),
            article: $this->articleRepository->findById($result->article_id),
        );

        $like->setId($result->id);
        return $like;
    }

    public function isExists(int $id): bool
    {
        try {
            $this->findById($id);
        } catch (LikeNotFoundException) {
            return false;
        }

        return true;
    }

    public function getByArticleId(int $articleId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE article_id = :articleId'
        );

        $statement->execute([
            ':articleId' => (string)$articleId,
        ]);

        $likes = [];
        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $like = new Like(
                $this->userRepository->findById($row->user_id),
                $this->articleRepository->findById($row->article_id),
            );
            $like->setId($row->id);
            $likes[] = $like;
        }

        return $likes;
    }
}
