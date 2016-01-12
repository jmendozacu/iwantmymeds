<?php
 
require_once 'shell/abstract.php';
require 'app/Mage.php';
 
Mage::app('admin')->setUseSessionInUrl(false);
 
class ManualCron extends Mage_Shell_Abstract {
    private $_defaultCronJobs = null;
 
    public function __construct() {
        parent::__construct();
        $collection = Mage::getModel('aoe_scheduler/collection_crons');
        $_defaultCronJobs= array();
        foreach ($collection->getItems() as $item) {
            $data = $item->getData();
            $this->_defaultCronJobs[$data['id']] = $data['model'];
        }
    }
 
    private function _runCron($cronId) {
        $cron = explode("::", $this->_defaultCronJobs[$cronId]);
        $cronFunction = "Mage::getModel('".$cron[0]."')->".$cron[1]."();";
        echo "RUNNING: ".$cronFunction.PHP_EOL;
        eval($cronFunction);
    }
 
    private function _runAllCrons() {
        foreach ($this->_defaultCronJobs as $key => $value) {
            $this->_runCron($key);
        }
    }
 
    private function _displayAvaliableCronjobs() {
        foreach ($this->_defaultCronJobs as $key => $value) {
            echo $key." -> ".$value.PHP_EOL;
        }
    }
 
    public function run() {
        $command = $this->getArg('command');
        if($command) {
            switch($command) {
            case "all":
                $this->_runAllCrons();
                break;
            case "crons":
                $this->_displayAvaliableCronjobs();
                break;
            default:
                if (isset($this->_defaultCronJobs[$command])) {
                    $this->_runCron($command);
                } else {
                    echo $this->usageHelp();
                }
                break;
            } //switch
 
        } else {
            echo $this->usageHelp();
        }
    }
 
    public function usageHelp()
    {
        return <<<USAGE
        Usage:  php manualcron.php --command [option]
 
        [option]
        help                        This help
 
        all         Run all cron jobs
        crons    Display all the avaliable cron jobs setup
        [specific_cron_id]    Run a specific cron, e.g. "php manualcron.php --command cronjob_name"
USAGE;
    }
}
 
$shell = new ManualCron();
$shell->run();
echo PHP_EOL;