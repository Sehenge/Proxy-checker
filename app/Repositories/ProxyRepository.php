<?php

namespace App\Repositories;

use App\Contract\ProxyContract;
use App\Models\Proxy;
use Illuminate\Database\Eloquent\Collection;

class ProxyRepository implements ProxyContract
{
    /**
     * Return all proxies
     */
    public function getAllProxies(): Collection
    {
        return Proxy::all();
    }

    public function getActiveProxyIpAndPort(): Collection
    {
        return Proxy::query()->where('is_active', true)->get(['ip', 'port']);
    }
}
