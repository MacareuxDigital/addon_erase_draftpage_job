<?php
namespace Concrete\Package\EraseDraftpageJob;

use Concrete\Core\Package\Package;
use Job;

class Controller extends Package
{
    protected $pkgHandle = 'erase_draftpage_job';
    protected $appVersionRequired = '5.7.4.2';
    protected $pkgVersion = '1.0.0';
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
    }
    public function upgrade()
    {
        $pkg = parent::upgrade();
        $this->installJobs($pkg);
    }

    protected function installJobs($pkg)
    {
        $jobHandle = 'erase_draftpage';
        $job = Job::getByHandle($jobHandle);
        if (!is_object($job)) {
            Job::installByPackage($jobHandle, $pkg);
        }
    }
}
