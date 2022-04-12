<?php

namespace App\Commands\SymfonyCommands;

use Faker\Generator;
use App\Entities\User\User;
use App\Commands\EntityCommand;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Commands\CreateUserCommandHandler;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private CreateUserCommandHandler $createUserCommandHandler,
        private CreateArticleCommandHandler $createArticleCommandHandler,
        private CreateCommentCommandHandler $createCommentCommandHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Users number',
            )
            ->addOption(
                'articles-number',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Articles number',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $usersNumber = $input->getOption('users-number') ?? 10;
        $articlesNumber = $input->getOption('articles-number') ?? 20;

        $users = [];
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getEmail());
        }

        $articles = [];
        foreach ($users as $user) {
            for ($i = 0; $i < $articlesNumber; $i++) {
                $article = $this->createFakeArticle($user);
                $articles[] = $article;
                $output->writeln('Article created: ' . $article->getTitle());
            }
        }

        foreach ($users as $user) {
            $commentsNumber = mt_rand(0, $articlesNumber);
            for ($i = 0; $i < $commentsNumber; $i++) {
                $comment = $this->createFakeComment($user, $articles[array_rand($articles)]);
                $comments[] = $comment;
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user =
            new User(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->email,
                $this->faker->password,
            );

        return $this->createUserCommandHandler->handle(new EntityCommand($user));
    }

    private function createFakeArticle(User $author): Article
    {
        $article = new Article(
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText,
        );

        return $this->createArticleCommandHandler->handle(new EntityCommand($article));
    }

    private function createFakeComment(User $author, Article $article): Comment
    {
        $comment = new Comment(
            $author,
            $article,
            $this->faker->sentence(6, true),
        );

        return $this->createCommentCommandHandler->handle(new EntityCommand($comment));
    }
}
