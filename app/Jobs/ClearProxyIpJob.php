<?php

namespace App\Jobs;

use App\Http\Business\ProxyIpBusiness;
use Carbon\Carbon;

class ClearProxyIpJob extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = "clear-ip";

    /**
     * 透明度
     *
     * @var
     */
    private $proxy_ip;

    /**
     * ProxyIpLocationJob constructor.
     * @param array $proxy_ip
     */
    public function __construct(array $proxy_ip)
    {
        $this->proxy_ip = $proxy_ip;
    }

    /**
     * @param ProxyIpBusiness $proxy_ip_business
     * @throws \App\Exceptions\JsonException
     * @author jiangxianli
     * @created_at 2019-10-23 16:47
     */
    public function handle(ProxyIpBusiness $proxy_ip_business)
    {
        try {
            //测速及可用性检查
            $speed = $proxy_ip_business->ipSpeedCheck($this->proxy_ip['ip'], $this->proxy_ip['port'], $this->proxy_ip['protocol']);
            //更新测速信息
            $proxy_ip_business->updateProxyIp($this->proxy_ip['unique_id'], [
                'speed'        => $speed,
                'validated_at' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $proxy_ip_business->deleteProxyIp($this->proxy_ip['unique_id']);
        }
    }
}
