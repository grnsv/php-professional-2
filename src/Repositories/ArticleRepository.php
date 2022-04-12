<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    public function __construct(
        Connection $connection,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger,
    ) {
        parent::__construct($connection);
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function findById(int $id): Article
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM articles WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getArticle($statement);
    }

    /**
     * @throws ArticleNotFoundException
     */
    private function getArticle(PDOStatement $statement): Article
    {
        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            $this->logger->error('Article not found');
            throw new ArticleNotFoundException('Article not found');
        }

        $article =  new Article(
            author: $this->userRepository->findById($result->author_id),
            title: $result->title,
            text: $result->text,
        );

        $article->setId($result->id);
        return $article;
    }

    public function isExists(int $id): bool
    {
        try {
            $this->findById($id);
        } catch (ArticleNotFoundException) {
            return false;
        }

        return true;
    }
}
