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
use App\Entities\Comment\Comment;
use App\Repositories\CommentRepository;
use App\Exceptions\CommentNotFoundException;
use App\Commands\CreateCommentCommandHandler;

class CreateCommentCommandTest extends TestCase
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
     * @throws CommentNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesCommentToDatabase($user, $article, $comment): void
    {
        $commentRepositoryStub = $this->createStub(CommentRepository::class);
        $connectionStub = $this->createStub(Connection::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $createCommentCommandHandler = new CreateCommentCommandHandler(
            $commentRepositoryStub,
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
                ':article_id' => $article->getId(),
                ':text' => $comment->getText(),
            ]);

        $command = new EntityCommand($comment);

        $createCommentCommandHandler->handle($command);
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

        $comment = new Comment(
            $user,
            $article,
            $this->faker->text(),
        );
        $comment->setId(mt_rand(1, mt_getrandmax()));

        return [$user, $article, $comment];
    }
}
