<?php

namespace App\Http\Controllers;

use App\Repositories\ProxyRepository;
use Illuminate\Database\Eloquent\Collection;

class ProxyController extends Controller
{
    public function __construct(private readonly ProxyRepository $proxyRepository) {}

    /**
     * @return Collection
     */
    public function getAllProxies()
    {
        return $this->proxyRepository->getAllProxies();
    }

    public function getActiveProxies()
    {
        return $this->proxyRepository->getActiveProxyIpAndPort();
    }
}
