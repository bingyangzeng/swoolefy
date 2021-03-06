<?php
/**
+----------------------------------------------------------------------
| swoolefy framework bases on swoole extension development, we can use it easily!
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
 */

namespace Swoolefy\AutoReload;

use Swoole\Process;
use Swoolefy\Core\Swfy;
use Swoolefy\Core\Process\AbstractProcess;

class ReloadProcess extends AbstractProcess {

    /**
     * @param Process $process
     * @throws \Exception
     */
    public function run(Process $process) {
        $config = Swfy::getConf();
        if(isset($config['reload_conf'])) {
            $reload_config = $config['reload_conf'];
            $autoReload = new Reload();
            if(isset($reload_config['after_seconds'])){
                $autoReload->setAfterSeconds((float)$reload_config['after_seconds']);
            }else {
                $autoReload->setAfterSeconds();
            }

            if(isset($reload_config['reload_file_types']) && is_array($reload_config['reload_file_types'])) {
                $autoReload->setReoloadFileType($reload_config['reload_file_types']);
            }else {
                $autoReload->setReoloadFileType();
            }

            if(isset($reload_config['ingore_dirs']) && is_array($reload_config['ingore_dirs'])) {
                $autoReload->setIgnoreDirs($reload_config['ingore_dirs']);
            }else {
                $autoReload->setIgnoreDirs();
            }

            // 初始化配置
            $autoReload->init();
            // 开始监听
            $autoReload->watch($reload_config['monitor_path'])->onReload($reload_config['callback']);
        }
    }

    public function onReceive($str, ...$args) {}

    public function onShutDown() {}
}