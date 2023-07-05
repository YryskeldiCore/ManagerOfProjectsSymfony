<?php

declare(strict_types=1);

namespace App\Command\User;

use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use App\Model\User\UseCase\SignUp\Confirm;
use Symfony\Component\Console\Command\Command;
class ConfirmCommand extends Command
{
    private UserFetcher $users;
    private Confirm\Manual\Handler $handler;
    public function __construct(UserFetcher $users, Confirm\Manual\Handler $handler)
    {
        $this->users = $users;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:confirm')
            ->setDescription('Confirms signed up user');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new \LogicException('User is not found');
        }

        $command = new Confirm\Manual\Command($user->id);
        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
    }
}
