<?php


namespace W2w\Test\ApieDoctrinePlugin\Providers;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use W2w\Lib\ApieDoctrinePlugin\ApieDoctrinePlugin;
use W2w\Lib\ApieDoctrinePlugin\Providers\ApieDoctrinePluginServiceProvider;

class ApieDoctrinePluginServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $results = null;
        
        $registry = $this->prophesize(ManagerRegistry::class);
        $registry->getDefaultManagerName()->willReturn('pizza');
        $registry->getManager('pizza')
            ->shouldBeCalled()
            ->willReturn($this->prophesize(EntityManagerInterface::class)->reveal());
        $registry->getManagers()
            ->willReturn([]);

        $app = $this->prophesize(Application::class);
        $app->get(ManagerRegistry::class)
            ->shouldBeCalled()
            ->will(function () use (&$registry) {
                return $registry->reveal();
            });
        $app->bind(ApieDoctrinePlugin::class, Argument::type(Closure::class))
            ->shouldBeCalled()
            ->will(function (array $args) use (&$results) {
                $results = $args[1];
            });
        $testItem = new ApieDoctrinePluginServiceProvider($app->reveal());
        $testItem->register();
        $this->assertInstanceof(Closure::class, $results);
        $this->assertInstanceOf(ApieDoctrinePlugin::class, $results());
    }
}
