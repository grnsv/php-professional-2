<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\User\User;
use PHPUnit\Framework\TestCase;
use App\Entities\Comment\Comment;
use App\Commands\CreateEntityCommand;
use App\Connections\ConnectorInterface;
use App\Exceptions\CommentNotFoundException;
use App\Commands\CreateCommentCommandHandler;
use App\Entities\Article\Article;

class CreateCommentCommandTest extends TestCase
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
                [$user, $article, $this->faker->text()],
            ];
    }

    /**
     * @throws CommentNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesCommentToDatabase($author, $article, $text): void
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
                ':article_id' => $article->getId(),
                ':text' => $text,
            ]);

        /**
         * @var ConnectorInterface $connectorStub
         */
        $createCommentCommandHandler = new CreateCommentCommandHandler($connectorStub);

        $command = new CreateEntityCommand(
            new Comment(
                $author,
                $article,
                $text
            )
        );

        $createCommentCommandHandler->handle($command);
    }
}
