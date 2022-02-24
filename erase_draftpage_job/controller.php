<?php
namespace Concrete\Package\EraseDraftpageJob;

use Concrete\Core\Package\Package;
use Concrete\Core\Job\Job;
use Concrete\Package\EraseDraftpageJob\Command\Task\Controller\EraseDraftsController;
use Concrete\Core\Command\Task\Manager;

class Controller extends Package
{
    protected $pkgHandle = 'erase_draftpage_job';
    protected $appVersionRequired = '5.7.4.2';
    protected $pkgVersion = '1.1.0';
    protected $pkgAutoloaderMapCoreExtensions = true;

    public function getPackageDescription()
    {
        return t('This is a simple job package to erase all draft pages. It would be useful for those who ended up having too many draft pages.');
    }

    public function getPackageName()
    {
        return t('Erase Draft Page Job');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installJobs($pkg);
        $currentVersion = $this->app->make('config')->get('concrete.version');
        if (version_compare($currentVersion , '8.9.9', '>')) {
            $this->installContentFile('config/tasks.xml');
        }
    }
    public function upgrade()
    {
        $pkg = parent::upgrade();
        $this->installJobs($pkg);
        $currentVersion = $this->app->make('config')->get('concrete.version');
        if (version_compare($currentVersion , '8.9.9', '>')) {
            $this->installContentFile('config/tasks.xml');
        }
    }

    protected function installJobs($pkg)
    {
        $jobHandle = 'erase_draftpage';
        $job = Job::getByHandle($jobHandle);
        if (!is_object($job)) {
            Job::installByPackage($jobHandle, $pkg);
        }
    }

    public function on_start()
    {
        $currentVersion = $this->app->make('config')->get('concrete.version');
        if (version_compare($currentVersion , '8.9.9', '>')) {
            $manager = $this->app->make(Manager::class);
            $manager->extend('erase_drafts',static function () {
                return new EraseDraftsController();
            });
        }

    }
}
