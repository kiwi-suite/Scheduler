<?php
declare(strict_types=1);

namespace KiwiSuite\Scheduler\Console;


use KiwiSuite\Contract\Command\CommandInterface;
use KiwiSuite\Scheduler\Task\TaskSubManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use KiwiSuite\Scheduler\Task\TaskMapping;

class TaskCommand extends Command implements CommandInterface
{
    private $taskMapping;

    private $taskSubManager;

    public function __construct(TaskMapping $taskMapping, TaskSubManager $taskSubManager)
    {
        $this->taskMapping = $taskMapping;
        $this->taskSubManager = $taskSubManager;
        parent::__construct(self::getCommandName());
    }

    protected function configure()
    {
        $this->addArgument('name',InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $taskName = $this->validTask($input->getArgument('name'));
        $task = $this->taskSubManager->get($taskName);
        $task->task();
    }

    public static function getCommandName()
    {
        return 'task:run';
    }

    private function validTask($name)
    {
        $tasks = [];
        $namespace = [];
        foreach ($this->taskMapping->getMapping() as $task) {
            $namespace[$task] = $tasks[] = ($this->taskSubManager->get($task))->getName();
        }
        if (!in_array($name, $tasks)) {
            throw new \InvalidArgumentException('Task does not exist');
        }
        $key = array_search($name, $namespace);
        return $key;
    }

}