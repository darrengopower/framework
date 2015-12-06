<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-10-18 16:28
 */
namespace Notadd\Foundation\Install\Bootstrap;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Routing\ControllerServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Notadd\Foundation\Cache\CacheServiceProvider;
use Notadd\Foundation\Console\ConsoleServiceProvider;
use Notadd\Foundation\Console\ConsoleSupportServiceProvider;
use Notadd\Foundation\Database\DatabaseServiceProvider;
use Notadd\Foundation\Extension\ExtensionServiceProvider;
use Notadd\Foundation\Http\FormRequestServiceProvider;
use Notadd\Foundation\Translation\TranslationServiceProvider;
use Notadd\Install\InstallServiceProvider;
class RegisterProviders {
    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function bootstrap(Application $app) {
        $app->register(ConsoleServiceProvider::class);
        $app->register(ConsoleSupportServiceProvider::class);
        $app->register(CacheServiceProvider::class);
        $app->register(ControllerServiceProvider::class);
        $app->register(CookieServiceProvider::class);
        $app->register(DatabaseServiceProvider::class);
        $app->register(EncryptionServiceProvider::class);
        $app->register(FilesystemServiceProvider::class);
        $app->register(HashServiceProvider::class);
        $app->register(MailServiceProvider::class);
        $app->register(PaginationServiceProvider::class);
        $app->register(PipelineServiceProvider::class);
        $app->register(QueueServiceProvider::class);
        $app->register(RedisServiceProvider::class);
        $app->register(SessionServiceProvider::class);
        $app->register(TranslationServiceProvider::class);
        $app->register(ValidationServiceProvider::class);
        $app->register(ViewServiceProvider::class);
        $app->register(FormRequestServiceProvider::class);
        $app->register(ExtensionServiceProvider::class);
        $app->register(InstallServiceProvider::class);
    }
}