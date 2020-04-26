<?php

namespace Concrete\Package\EraseDraftpageJob\Job;

use QueueableJob;
use ZendQueue\Queue as ZendQueue;
use ZendQueue\Message as ZendQueueMessage;
use Exception;
use Concrete\Core\Page\Page;
use Core;

class EraseDraftpage extends QueueableJob
{
    public $jSupportsQueue = true;

    public function getJobName()
    {
        return t('Erase Draft Pages');
    }

    public function getJobDescription()
    {
        return t('This job will erase all draft pages. It would be useful for those who ended up having too many draft pages.');
    }

    public function start(ZendQueue $q)
    {
        $currentVersion = Core::make('config')->get('concrete.version');
        if (version_compare($currentVersion , '8.2.0', 'lt')) {
            $pageDrafts = Page::getDrafts();
        } else {
            $site = Core::make('site')->getSite();
            $pageDrafts = Page::getDrafts($site);
        }
        foreach ($pageDrafts as $pageDraft) {
            $q->send($pageDraft->getCollectionID());
        }
    }

    public function processQueueItem(ZendQueueMessage $msg)
    {
        $pageDraft = Page::getByID($msg->body);
        if ($pageDraft->isPageDraft()) {
            $pageDraft->delete();
        } else {
            throw new Exception(t('Error occurred while getting the Page object of pID: %s', $msg->body));
        }
    }

    public function finish(ZendQueue $q)
    {
        return t('Finished erasing draft pages.');
    }
}
