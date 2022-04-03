<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\Like\Like;
use App\Entities\User\User;
use PHPUnit\Framework\TestCase;
use App\Entities\Article\Article;
use App\Repositories\LikeRepository;
use App\Commands\CreateEntityCommand;
use App\Exceptions\LikeNotFoundException;
use App\Commands\CreateLikeCommandHandler;

class CreateLikeCommandTest extends TestCase
{
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
        return
            [
                [$user, $article],
            ];
    }

    /**
     * @throws LikeNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesLikeToDatabase($user, $article): void
    {
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var MockObject $statementMock
         */
        $statementMock = $this->createMock(PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':author_id' => $user->getId(),
                ':article_id' => $article->getId(),
            ]);

        /**
         * @var Connection $connectionStub
         */
        $createLikeCommandHandler = new CreateLikeCommandHandler(new LikeRepository($connectionStub), $connectionStub);

        $command = new CreateEntityCommand(
            new Like(
                $user,
                $article,
            )
        );

        $createLikeCommandHandler->handle($command);
    }
}
