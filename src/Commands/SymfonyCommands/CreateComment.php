<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\EntityCommand;
use App\Entities\Comment\Comment;
use App\Commands\CreateCommentCommandHandler;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateComment extends Command
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private CommentRepositoryInterface $commentRepository,
        private UserRepositoryInterface $userRepository,
        private ArticleRepositoryInterface $articleRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('comment:create')
            ->setDescription('Creates new comment')
            ->addArgument('authorId', InputArgument::REQUIRED, 'author ID')
            ->addArgument('articleId', InputArgument::REQUIRED, 'article ID')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $output->writeln('Create comment command started');

        $comment = new Comment(
            $this->userRepository->findById($input->getArgument('authorId')),
            $this->articleRepository->findById($input->getArgument('articleId')),
            $input->getArgument('text'),
        );

        $comment = $this->createCommentCommandHandler->handle(new EntityCommand($comment));

        $output->writeln('Comment created: ' . $comment->getId());
        return Command::SUCCESS;
    }
}
