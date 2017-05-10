<?php

/*
 * This file is part of Swap.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swap;

use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\ExchangeRateQueryBuilder;

/**
 * Swap is an easy to use facade to retrieve exchange rates from various services.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Swap
{
    /**
     * The exchange rate provider.
     *
     * @var ExchangeRateProvider
     */
    private $exchangeRateProvider;

    /**
     * Creates a new Swap.
     *
     * @param ExchangeRateProvider $exchangeRateProvider
     */
    public function __construct(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    /**
     * Quotes a currency pair.
     *
     * @param string $currencyPair The currency pair like "EUR/USD"
     * @param array  $options      An array of query options
     *
     * @return \Exchanger\ExchangeRate
     */
    public function latest($baseCurrency, $quoteCurrency, array $options = [])
    {
        return $this->quote($baseCurrency, $quoteCurrency, null, $options);
    }

    /**
     * Quotes a currency pair.
     *
     * @param string             $currencyPair The currency pair like "EUR/USD"
     * @param \DateTimeInterface $date         An optional date for historical rates
     * @param array              $options      An array of query options
     *
     * @return \Exchanger\ExchangeRate
     */
    private function quote($baseCurrency, $quoteCurrency,  \DateTimeInterface $date = null, array $options = [])
    {
        $exchangeQueryBuilder = new ExchangeRateQueryBuilder($baseCurrency, $quoteCurrency);

        if (null !== $date) {
            $exchangeQueryBuilder->setDate($date);
        }

        foreach ($options as $name => $value) {
            $exchangeQueryBuilder->addOption($name, $value);
        }

        $query = $exchangeQueryBuilder->build();

        return $this->exchangeRateProvider->getExchangeRate($query);
    }
}
