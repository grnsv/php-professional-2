<?php

namespace App\Repositories;

use PDO;
use PDOStatement;
use App\Entities\User\User;
use App\Commands\GetCommand;
use App\Entities\Article\Article;
use App\Factories\EntityManagerFactory;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * @throws ArticleNotFoundException
     */
    public function get(int $id): Article
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
            throw new ArticleNotFoundException('Article not found');
        }

        /**
         * @var EntityManagerFactoryInterface $entityMangerFactory
         */
        $entityMangerFactory = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityMangerFactory->getRepository(User::class));
        $author = $command->handle($result->author_id);
        $article = new Article($author, $result->title, $result->text);
        $article->setId($result->id);
        return $article;
    }
}
