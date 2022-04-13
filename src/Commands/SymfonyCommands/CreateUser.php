<?php

namespace App\Commands\SymfonyCommands;

use App\Entities\User\User;
use App\Commands\EntityCommand;
use App\Commands\CreateUserCommandHandler;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private CreateUserCommandHandler $createUserCommandHandler,
        private UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Creates new user')
            ->addArgument('firstName', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $output->writeln('Create user command started');
        $email = $input->getArgument('email');

        if ($this->userRepository->isUserExists($email)) {
            $output->writeln("User already exists: $email");
            return Command::FAILURE;
        }

        $user = new User(
            $input->getArgument('firstName'),
            $input->getArgument('lastName'),
            $email,
            $input->getArgument('password'),
        );

        $user = $this->createUserCommandHandler->handle(new EntityCommand($user));

        $output->writeln('User created: ' . $user->getId());
        return Command::SUCCESS;
    }
}
