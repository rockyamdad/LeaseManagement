<?php
namespace UserBundle\EventListener;

use Knp\Menu\MenuItem;
use Rbs\Bundle\CoreBundle\Event\ConfigureMenuEvent;
use Rbs\Bundle\CoreBundle\EventListener\ContextAwareListener;

class ConfigureMenuListener extends ContextAwareListener
{
    /**
     * @param ConfigureMenuEvent $event
     * @return MenuItem
     */
    public function onMenuConfigureMain(ConfigureMenuEvent $event)
    {
        /** @var MenuItem $menu */
        $menu = $event->getMenu();
        if ($this->authorizationChecker->isGranted(array('ROLE_USER_VIEW', 'ROLE_USER_CREATE', 'ROLE_GROUP_VIEW', 'ROLE_GROUP_CREATE'))) {
            $menu->addChild('User', array('route' => ''))
                ->setAttribute('dropdown', true)
                ->setAttribute('icon', 'fa fa-user')
                ->setLinkAttribute('data-hover', 'dropdown');

            if ($this->authorizationChecker->isGranted(array('ROLE_USER_VIEW', 'ROLE_USER_CREATE'))) {
                $menu['User']->addChild('Users', array('route' => 'users_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                $menu['User']->addChild('User Create', array('route' => 'user_create'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('user_update')) {
                    $menu['User']->getChild('Users')->setCurrent(true);
                }
            }

            if ($this->authorizationChecker->isGranted(array('ROLE_GROUP_VIEW', 'ROLE_GROUP_CREATE'))) {
                $menu['User']->addChild('User Groups', array('route' => 'groups_home'))
                    ->setAttribute('icon', 'fa fa-th-list');
                if ($this->isMatch('group')) {
                    $menu['User']->getChild('User Groups')->setCurrent(true);
                }
            }
        }

        return $menu;
    }
}