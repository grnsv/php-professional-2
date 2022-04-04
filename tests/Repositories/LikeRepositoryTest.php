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
        $user = new User(
            $this->faker->userName(),
            $this->faker->word(),
            $this->faker->email()
        );
        $user->setId(mt_rand(1, mt_getrandmax()));
        $article = new Article(
            $user,
            $this->faker->word(),
            $this->faker->text()
        );
        $article->setId(mt_rand(1, mt_getrandmax()));

        $likesQty = mt_rand(1, 10);
        $likes = [];

        for ($i = 0; $i < $likesQty; $i++) {
            $user = new User(
                $this->faker->userName(),
                $this->faker->word(),
                $this->faker->email()
            );
            $user->setId(mt_rand(1, mt_getrandmax()));

            $likes[] = new Like(
                $user,
                $article,
            );
        }

        return
            [
                [$article->getId(), $likes],
            ];
    }

    public function testItThrowsAnExceptionWhenLikeNotFound(): void
    {
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $statementStub
         */
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);

        /**
         * @var Connection $connectionStub
         */
        $repository = new LikeRepository($connectionStub, $this->getLogger());

        $this->expectException(LikeNotFoundException::class);
        $this->expectExceptionMessage('Like not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItReturnsLikesByArticleId($articleId, $expectedValue): void
    {
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $statementStub
         */
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn($expectedValue);

        /**
         * @var Connection $connectionStub
         */
        $repository = new LikeRepository($connectionStub, $this->getLogger());
        $likes = $repository->getByArticleId($articleId);

        $this->assertEquals($expectedValue, $likes);
    }
}
