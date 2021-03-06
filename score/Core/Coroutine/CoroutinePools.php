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

namespace Swoolefy\Core\Coroutine;

use Swoolefy\Core\BaseServer;

class CoroutinePools {

    use \Swoolefy\Core\SingletonTrait;

    private $pools = [];

    /**
     * 在workerStart可以创建一个协程池Channel
        \Swoolefy\Core\Coroutine\CoroutinePools::getInstance()->addPool('redis', function($pool_name) {
            $redis = new \App\CoroutinePools\RedisPools();
            $redis->setMaxPoolsNum();
            $redis->setMinPoolsNum();
            $redis->registerPools($pool_name);
            return $redis;
        });
     * @param  string  $name
     * @param  PoolsHandler   $handler
     * @throws mixed
     */
    public function addPool(string $pool_name, $handler) {
        $AppConf = BaseServer::getAppConf();
        $pool_name = trim($pool_name);
        if(isset($AppConf['enable_component_pools']) && is_array($AppConf['enable_component_pools']) && !empty($AppConf['enable_component_pools'])) {
            if(!in_array($pool_name, $AppConf['enable_component_pools'])) {
                throw new \Exception("pool_name={$pool_name} must in app conf['enable_component_pools'] value");
            }
        }
        if(!isset($this->pools[$pool_name])) {
            if($handler instanceof PoolsHandler) {
                $handler_pool_name = $handler->getPoolName();
                if($handler_pool_name == $pool_name) {
                    $this->pools[$pool_name] = $handler;
                }else {
                    $class = get_class($handler);
                    throw new \Exception(__CLASS__."::addPool() of First Param 'pool_name'={$pool_name}, but {$class}::pool_name = {$handler_pool_name}, so the two are not equal");
                }
            }else if($handler instanceof \Closure) {
                $args = [$pool_name];
                $this->pools[$pool_name] = call_user_func_array($handler, $args);
            }
        }
    }

    /**
     * getChannel
     * @param    string   $name
     * @return   mixed
     */
    public function getPool(string $pool_name) {
        $pool_name = trim($pool_name);
        if(isset($this->pools[$pool_name])) {
            return $this->pools[$pool_name];
        }else{
            return null;
        }
    }
}