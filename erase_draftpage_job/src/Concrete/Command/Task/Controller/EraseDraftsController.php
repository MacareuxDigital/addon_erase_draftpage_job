<?php
namespace Concrete\Package\EraseDraftpageJob\Command\Task\Controller;


use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Page\Command\DeletePageCommand;
use Concrete\Core\User\User;


class EraseDraftsController extends AbstractController {

    public function getName(): string
    {
        return t('Erase Draft Pages');
    }

    public function getDescription(): string
    {
        return t('This task will erase all draft pages. It would be useful for those who ended up having too many draft pages.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {

        $user = app(User::class);
        $userID = ((int) $user->getUserID())?: null;
        $site = app('site')->getSite();
        $pageDrafts = Page::getDrafts($site);
        $batch = Batch::create(t('Erase Draft Pages'));

        foreach ($pageDrafts as $pageDraft) {
            $batch->add(new DeletePageCommand($pageDraft->getCollectionID(),$userID));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Started Erasing Draft Pages.'));

    }
}
