<?php

namespace Tests\Repositories;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\Like\Like;
use App\Entities\User\User;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Entities\Article\Article;
use App\Repositories\LikeRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Exceptions\LikeNotFoundException;

class LikeRepositoryTest extends TestCase
{
    use LoggerTrait;

    private Generator $faker;

    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = '',
    ) {
        $this->faker = Factory::create();
        parent::__construct($name, $data, $dataName);
    }

    public function argumentsProvider(): iterable
    {
        return
            [
                $this->getTestData(),
                $this->getTestData(),
                $this->getTestData(),
            ];
    }

    public function testItThrowsAnExceptionWhenLikeNotFound(): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $repository = new LikeRepository(
            $connectionStub,
            $this->createStub(UserRepository::class),
            $this->createStub(ArticleRepository::class),
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        /**
         * @var Stub $statementStub
         */
        $statementStub->method('fetch')->willReturn(false);

        $this->expectException(LikeNotFoundException::class);
        $this->expectExceptionMessage('Like not found');

        $repository->findById(mt_rand(1, mt_getrandmax()));
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItReturnsLikesByArticleId($article, $likes, $users, $rows): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $userRepository = $this->createStub(UserRepository::class);
        $articleRepository = $this->createStub(ArticleRepository::class);

        $likeRepository = new LikeRepository(
            $connectionStub,
            $userRepository,
            $articleRepository,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        /**
         * @var Stub $statementStub
         */
        $statementStub->method('fetch')->willReturnOnConsecutiveCalls(...$rows);

        /**
         * @var Stub $userRepository
         */
        $userRepository->method('findById')->willReturnOnConsecutiveCalls(...$users);

        /**
         * @var Stub $articleRepository
         */
        $articleRepository->method('findById')->willReturn($article);

        $result = $likeRepository->getByArticleId($article->getId());

        $this->assertEquals($likes, $result);
    }

    private function getTestData(): array
    {
        $author = new User(
            $this->faker->firstName(),
            $this->faker->lastName(),
            $this->faker->email(),
            $this->faker->password(),
        );
        $author->setId(mt_rand(1, mt_getrandmax()));

        $article = new Article(
            $author,
            $this->faker->title(),
            $this->faker->text(),
        );
        $article->setId(mt_rand(1, mt_getrandmax()));

        $likesQty = mt_rand(1, 10);
        $users = [];
        $likes = [];
        $rows = [];

        for ($i = 0; $i < $likesQty; $i++) {
            $user = new User(
                $this->faker->firstName(),
                $this->faker->lastName(),
                $this->faker->email(),
                $this->faker->password(),
            );
            $user->setId(mt_rand(1, mt_getrandmax()));
            $users[] = $user;

            $like = new Like(
                $user,
                $article,
            );
            $like->setId(mt_rand(1, mt_getrandmax()));
            $likes[] = $like;

            $rows[] = (object) [
                'id' => $like->getId(),
                'user_id' => $like->getUser()->getId(),
                'article_id' => $like->getArticle()->getId(),
            ];
        }

        return [$article, $likes, $users, $rows];
    }
}
