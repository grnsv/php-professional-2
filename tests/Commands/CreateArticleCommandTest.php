<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\User\User;
use Tests\Traits\LoggerTrait;
use App\Commands\EntityCommand;
use PHPUnit\Framework\TestCase;
use App\Entities\Article\Article;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;
use App\Commands\CreateArticleCommandHandler;

class CreateArticleCommandTest extends TestCase
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
     * @throws ArticleNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesArticleToDatabase($user, $article): void
    {
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);
        $connectionStub = $this->createStub(Connection::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $createArticleCommandHandler = new CreateArticleCommandHandler(
            $articleRepositoryStub,
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
                ':author_id' => $user->getId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText(),
            ]);

        $command = new EntityCommand($article);

        $createArticleCommandHandler->handle($command);
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

        return [$user, $article];
    }
}
