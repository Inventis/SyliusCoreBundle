<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use SM\Callback\CallbackFactoryInterface;
use SM\Callback\CascadeTransitionCallback;
use SM\Factory\FactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Marks WinzouStateMachineBundle's services as public for compatibility with both Symfony 3.4 and 4.0+.
 * Aliases FQCN-based services for backwards compatibility of Winzou/StateMachineBundle 0.4 with 0.3
 *
 * @see https://github.com/winzou/StateMachineBundle/pull/44
 * @see https://github.com/winzou/StateMachineBundle/commit/f515c9302783ef2575570d33b20aefa1eb265afb
 */
final class WinzouStateMachinePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $winzouServiceMap = [
            'sm.factory' => FactoryInterface::class,
            'sm.callback_factory' => CallbackFactoryInterface::class,
            'sm.callback.cascade_transition' => CascadeTransitionCallback::class,
        ];
        foreach ($winzouServiceMap as $serviceId => $interfaceFQCN) {
            if (!$container->hasDefinition($serviceId)) {
                $container->setAlias($serviceId, $interfaceFQCN);
            }
            try {
                $container->findDefinition($serviceId)->setPublic(true);
            } catch (ServiceNotFoundException $exception) {
            }
        }
    }
}
