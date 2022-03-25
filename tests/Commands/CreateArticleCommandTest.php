<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\User\User;
use PHPUnit\Framework\TestCase;
use App\Entities\Article\Article;
use App\Commands\CreateEntityCommand;
use App\Connections\ConnectorInterface;
use App\Exceptions\ArticleNotFoundException;
use App\Commands\CreateArticleCommandHandler;

class CreateArticleCommandTest extends TestCase
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
        return
            [
                [$user, $this->faker->word(), $this->faker->text()],
            ];
    }

    /**
     * @throws ArticleNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesArticleToDatabase($author, $title, $text): void
    {
        /**
         * @var Stub $connectorStub
         */
        $connectorStub = $this->createStub(ConnectorInterface::class);
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var MockObject $statementMock
         */
        $statementMock = $this->createMock(PDOStatement::class);

        $connectorStub->method('getConnection')->willReturn($connectionStub);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':author_id' => $author->getId(),
                ':title' => $title,
                ':text' => $text,
            ]);

        /**
         * @var ConnectorInterface $connectorStub
         */
        $createArticleCommandHandler = new CreateArticleCommandHandler($connectorStub);

        $command = new CreateEntityCommand(
            new Article(
                $author,
                $title,
                $text
            )
        );

        $createArticleCommandHandler->handle($command);
    }
}
