<?php

namespace Ruudk\Payment\MultisafepayBundle\CacheWarmer;

use Omnipay\MultiSafepay\Gateway;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class IdealCacheWarmer extends CacheWarmer
{
    /**
     * @var \Omnipay\MultiSafepay\Gateway
     */
    private $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        try {
            $banks = $this->gateway->fetchIssuers()->send()->getIssuers();

            $this->writeCacheFile($cacheDir . '/ruudk_payment_multisafepay_ideal.php', sprintf('<?php return %s;', var_export($banks, true)));
        } catch(\Exception $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }
}