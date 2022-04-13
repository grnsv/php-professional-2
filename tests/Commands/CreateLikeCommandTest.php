<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\Like\Like;
use App\Entities\User\User;
use Tests\Traits\LoggerTrait;
use App\Commands\EntityCommand;
use PHPUnit\Framework\TestCase;
use App\Entities\Article\Article;
use App\Repositories\LikeRepository;
use App\Exceptions\LikeNotFoundException;
use App\Commands\CreateLikeCommandHandler;

class CreateLikeCommandTest extends TestCase
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

    /**
     * @throws LikeNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesLikeToDatabase($user, $article, $like): void
    {
        $likeRepositoryStub = $this->createStub(LikeRepository::class);
        $connectionStub = $this->createStub(Connection::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $createLikeCommandHandler = new CreateLikeCommandHandler(
            $likeRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementMock);

        /**
         * @var MockObject $statementMock
         */
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':user_id' => $user->getId(),
                ':article_id' => $article->getId(),
            ]);

        $command = new EntityCommand($like);

        $createLikeCommandHandler->handle($command);
    }

    private function getTestData(): array
    {
        $user = new User(
            $this->faker->firstName(),
            $this->faker->lastName(),
            $this->faker->email(),
            $this->faker->password(),
        );
        $user->setId(mt_rand(1, mt_getrandmax()));

        $article = new Article(
            $user,
            $this->faker->title(),
            $this->faker->text(),
        );
        $article->setId(mt_rand(1, mt_getrandmax()));

        $like = new Like(
            $user,
            $article,
        );
        $like->setId(mt_rand(1, mt_getrandmax()));

        return [$user, $article, $like];
    }
}
