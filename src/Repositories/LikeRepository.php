<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Commands\GetCommand;
use Psr\Log\LoggerInterface;
use App\Entities\Article\Article;
use App\Factories\EntityManagerFactory;
use App\Exceptions\LikeNotFoundException;

class LikeRepository extends EntityRepository implements LikeRepositoryInterface
{
    public function __construct(
        Connection $connection,
        private LoggerInterface $logger,
    ) {
        parent::__construct($connection);
    }

    /**
     * @throws LikeNotFoundException
     */
    public function get(int $id): Like
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

        /**
         * @var EntityManagerFactoryInterface $entityMangerFactory
         */
        $entityMangerFactory = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityMangerFactory->getRepository(User::class));
        $author = $command->handle($result->author_id);
        $command = new GetCommand($entityMangerFactory->getRepository(Article::class));
        $article = $command->handle($result->article_id);
        $like = new Like($author, $article);
        $like->setId($result->id);
        return $like;
    }

    public function isExists(int $id): bool
    {
        try {
            $this->get($id);
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

        $likes = $statement->fetch(PDO::FETCH_ASSOC);

        return $likes;
    }
}
