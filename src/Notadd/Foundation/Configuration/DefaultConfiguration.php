<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-11-23 23:26
 */
namespace Notadd\Foundation\Configuration;
use Illuminate\Config\Repository;
use Notadd\Foundation\Auth\Models\User;
class DefaultConfiguration {
    private $config;
    public function __construct(Repository $config) {
        $this->config = $config;
    }
    public function loadApplicationConfiguration() {
        $this->config->set('app.url', 'http://localhost');
        $this->config->set('app.timezone', 'UTC+8');
        $this->config->set('app.locale', 'en');
        $this->config->set('app.fallback_locale', 'en');
        $this->config->set('app.key', 'GERojpSdTnQQbr77s5iXIa1c7Ne7NO4d');
        $this->config->set('app.cipher', MCRYPT_RIJNDAEL_128);
        $this->config->set('app.log', 'daily');
    }
    public function loadAuthConfiguration() {
        $this->config->set('auth.driver', 'eloquent');
        $this->config->set('auth.model', User::class);
        $this->config->set('auth.table', 'users');
        $this->config->set('auth.password.email', 'admin::emails.password');
        $this->config->set('auth.password.table', 'password_resets');
        $this->config->set('auth.password.expire', 60);
    }
    public function loadBroadcastingConfiguration() {
        $this->config->set('broadcasting.default', 'pusher');
        $this->config->set('broadcasting.connections.pusher.driver', 'pusher');
        $this->config->set('broadcasting.connections.pusher.key', '');
        $this->config->set('broadcasting.connections.pusher.secret', '');
        $this->config->set('broadcasting.connections.pusher.app_id', '');
        $this->config->set('broadcasting.connections.redis.driver', 'redis');
        $this->config->set('broadcasting.connections.redis.connection', 'default');
        $this->config->set('broadcasting.connections.log.driver', 'log');
    }
    public function loadCacheConfiguration() {
        $this->config->set('cache.default', 'file');
        $this->config->set('cache.stores.apc.driver', 'apc');
        $this->config->set('cache.stores.array.driver', 'array');
        $this->config->set('cache.stores.database.driver', 'database');
        $this->config->set('cache.stores.database.table', 'cache');
        $this->config->set('cache.stores.database.connection', null);
        $this->config->set('cache.stores.file.driver', 'file');
        $this->config->set('cache.stores.file.path', storage_path('framework/cache'));
        $this->config->set('cache.stores.memcached.driver', 'memcached');
        $this->config->set('cache.stores.memcached.servers', [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100,]]);
        $this->config->set('cache.stores.redis.driver', 'redis');
        $this->config->set('cache.stores.redis.connection', 'default');
        $this->config->set('cache.prefix', 'notadd');
    }
    public function loadCompileConfiguration() {
        $this->config->set('compile.files', []);
        $this->config->set('compile.providers', []);
    }
    public function loadFilesystemsConfiguration() {
        $this->config->set('filesystems.default', 'local');
        $this->config->set('filesystems.cloud', 's3');
        $this->config->set('filesystems.disks.local.driver', 'local');
        $this->config->set('filesystems.disks.local.root', storage_path('app'));
        $this->config->set('filesystems.disks.ftp.driver', 'ftp');
        $this->config->set('filesystems.disks.ftp.host', 'ftp.example.com');
        $this->config->set('filesystems.disks.ftp.username', 'your-username');
        $this->config->set('filesystems.disks.ftp.password', 'your-password');
        $this->config->set('filesystems.disks.s3.driver', 's3');
        $this->config->set('filesystems.disks.s3.key', 'your-key');
        $this->config->set('filesystems.disks.s3.secret', 'your-secret');
        $this->config->set('filesystems.disks.s3.region', 'your-region');
        $this->config->set('filesystems.disks.s3.bucket', 'your-bucket');
        $this->config->set('filesystems.disks.rackspace.driver', 'rackspace');
        $this->config->set('filesystems.disks.rackspace.username', 'your-username');
        $this->config->set('filesystems.disks.rackspace.key', 'your-key');
        $this->config->set('filesystems.disks.rackspace.container', 'your-container');
        $this->config->set('filesystems.disks.rackspace.endpoint', 'https://identity.api.rackspacecloud.com/v2.0/');
        $this->config->set('filesystems.disks.rackspace.region', 'IAD');
        $this->config->set('filesystems.disks.rackspace.url_type', 'publicURL');
    }
    public function loadMailConfiguration() {
        $this->config->set('mail.driver', 'smtp');
        $this->config->set('mail.host', 'smtp.qq.com');
        $this->config->set('mail.port', '587');
        $this->config->set('mail.from.address', '269044570@qq.com');
        $this->config->set('mail.from.name', 'TwilRoad');
        $this->config->set('mail.encryption', 'tls');
        $this->config->set('mail.username', '269044570@qq.com');
        $this->config->set('mail.password', 'hshd135078');
        $this->config->set('mail.sendmail', '/usr/sbin/sendmail -bs');
        $this->config->set('mail.pretend', false);
    }
    public function loadQueueConfiguration() {
        $this->config->set('queue.default', 'sync');
        $this->config->set('queue.connections.sync.driver', 'sync');
        $this->config->set('queue.connections.database.driver', 'database');
        $this->config->set('queue.connections.database.table', 'jobs');
        $this->config->set('queue.connections.database.queue', 'default');
        $this->config->set('queue.connections.database.expire', 60);
        $this->config->set('queue.connections.beanstalkd.driver', 'beanstalkd');
        $this->config->set('queue.connections.beanstalkd.host', 'localhost');
        $this->config->set('queue.connections.beanstalkd.queue', 'default');
        $this->config->set('queue.connections.beanstalkd.ttr', 60);
        $this->config->set('queue.connections.sqs.driver', 'sqs');
        $this->config->set('queue.connections.sqs.key', 'your-public-key');
        $this->config->set('queue.connections.sqs.secret', 'your-secret-key');
        $this->config->set('queue.connections.sqs.queue', 'your-queue-url');
        $this->config->set('queue.connections.sqs.region', 'us-east-1');
        $this->config->set('queue.connections.iron.driver', 'iron');
        $this->config->set('queue.connections.iron.host', 'mq-aws-us-east-1.iron.io');
        $this->config->set('queue.connections.iron.token', 'your-token');
        $this->config->set('queue.connections.iron.project', 'your-project-id');
        $this->config->set('queue.connections.iron.queue', 'your-queue-name');
        $this->config->set('queue.connections.iron.encrypt', true);
        $this->config->set('queue.connections.redis.driver', 'redis');
        $this->config->set('queue.connections.redis.connection', 'default');
        $this->config->set('queue.connections.redis.queue', 'default');
        $this->config->set('queue.connections.redis.expire', 60);
        $this->config->set('queue.failed.database', 'mysql');
        $this->config->set('queue.failed.table', 'failed_jobs');
    }
    public function loadSessionConfiguration() {
        $this->config->set('session.driver', 'file');
        $this->config->set('session.lifetime', 120);
        $this->config->set('session.expire_on_close', false);
        $this->config->set('session.encrypt', false);
        $this->config->set('session.files', storage_path('framework/sessions'));
        $this->config->set('session.connection', null);
        $this->config->set('session.table', 'sessions');
        $this->config->set('session.lottery', [2, 100]);
        $this->config->set('session.cookie', 'notadd_session');
        $this->config->set('session.path', '/');
        $this->config->set('session.domain', null);
        $this->config->set('session.secure', false);
    }
    public function loadServicesConfiguration() {
        $this->config->set('services.mailgun.domain', '');
        $this->config->set('services.mailgun.secret', '');
        $this->config->set('services.mandrill.secret', '');
        $this->config->set('services.ses.key', '');
        $this->config->set('services.ses.secret', '');
        $this->config->set('services.ses.region', 'us-east-1');
        $this->config->set('services.stripe.key', '');
        $this->config->set('services.stripe.secret', '');
    }
    public function loadViewConfiguration() {
        $this->config->set('view.paths', [realpath(base_path('../template/default/views'))]);
        $this->config->set('view.compiled', realpath(storage_path('framework/views')));
    }
}