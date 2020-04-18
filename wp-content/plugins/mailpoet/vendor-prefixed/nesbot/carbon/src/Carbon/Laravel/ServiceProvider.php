<?php

namespace MailPoetVendor\Carbon\Laravel;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Illuminate\Events\Dispatcher;
use MailPoetVendor\Illuminate\Events\EventDispatcher;
use MailPoetVendor\Illuminate\Translation\Translator as IlluminateTranslator;
use MailPoetVendor\Symfony\Component\Translation\Translator;
class ServiceProvider extends \MailPoetVendor\Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $service = $this;
        $events = $this->app['events'];
        if ($events instanceof \MailPoetVendor\Illuminate\Events\EventDispatcher || $events instanceof \MailPoetVendor\Illuminate\Events\Dispatcher) {
            $events->listen(\class_exists('MailPoetVendor\\Illuminate\\Foundation\\Events\\LocaleUpdated') ? 'Illuminate\\Foundation\\Events\\LocaleUpdated' : 'locale.changed', function () use($service) {
                $service->updateLocale();
            });
            $service->updateLocale();
        }
    }
    public function updateLocale()
    {
        $translator = $this->app['translator'];
        if ($translator instanceof \MailPoetVendor\Symfony\Component\Translation\Translator || $translator instanceof \MailPoetVendor\Illuminate\Translation\Translator) {
            \MailPoetVendor\Carbon\Carbon::setLocale($translator->getLocale());
        }
    }
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
}
