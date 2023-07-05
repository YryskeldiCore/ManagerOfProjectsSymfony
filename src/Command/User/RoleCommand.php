<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\Entity\User\Role;
use App\Model\User\UseCase\Role\Handler;
use App\Model\User\UseCase\SignUp\Confirm;
use App\ReadModel\User\UserFetcher;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleCommand extends Command
{
    private UserFetcher $users;
    private ValidatorInterface $validator;
    private Handler $handler;
    public function __construct(UserFetcher $users, ValidatorInterface $validator, Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user:role')
            ->setDescription('Changes user role');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new \LogicException('Email is not found');
        }

        $command = new \App\Model\User\UseCase\Role\Command($user->id);

        $roles = [Role::USER, Role::ADMIN];
        $command->role = $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            /**
             * @var ConstraintViolation $violation
             */
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage() . '</error>');
            }
            return;
        }

        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
    }
}
