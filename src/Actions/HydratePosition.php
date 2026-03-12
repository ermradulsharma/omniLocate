<?php

declare(strict_types=1);

namespace Skywalker\Location\Actions;

use Skywalker\Location\DataTransferObjects\Position;
use Skywalker\Support\Foundation\Action;

class HydratePosition extends Action
{
    /**
     * Execute the action.
     *
     * @param  mixed  ...$args
     * @return Position
     */
    public function execute(...$args): Position
    {
        /** @var Position $position */
        $position = $args[0];

        $position->currencyCode = $this->getCurrencyCode($position->countryCode);
        $position->language = $this->getLanguageCode($position->countryCode);

        if (! $position->connectionType && $position->ip) {
            $position->connectionType = 'Unknown';
        }

        return $position;
    }

    /**
     * Get the currency code for the given country code.
     */
    protected function getCurrencyCode(?string $countryCode): ?string
    {
        $currencies = [
            'US' => 'USD', 'IN' => 'INR', 'GB' => 'GBP', 'CA' => 'CAD', 'AU' => 'AUD',
            'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR', 'JP' => 'JPY',
            'BR' => 'BRL', 'CN' => 'CNY', 'RU' => 'RUB',
        ];

        return $currencies[strtoupper($countryCode ?? '')] ?? null;
    }

    /**
     * Get the language code for the given country code.
     */
    protected function getLanguageCode(?string $countryCode): ?string
    {
        $languages = [
            'US' => 'en', 'GB' => 'en', 'IN' => 'hi', 'DE' => 'de', 'FR' => 'fr',
            'ES' => 'es', 'IT' => 'it', 'JP' => 'ja', 'CN' => 'zh', 'RU' => 'ru', 'BR' => 'pt',
        ];

        return $languages[strtoupper($countryCode ?? '')] ?? null;
    }
}
