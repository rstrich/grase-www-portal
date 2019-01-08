<?php

namespace App\Menu;

use App\Entity\Radius\Group;
use Doctrine\ORM\EntityManagerInterface;
use Pd\MenuBundle\Builder\ItemInterface;
use Pd\MenuBundle\Builder\Menu;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainMenu extends Menu
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * Override
     */
    public function createMenu(array $options = []): ItemInterface
    {
        // Create Root Item
        $menu = $this
            ->createRoot('settings_menu', true) // Create event is "settings_menu.event"
                ->setListAttr([])

            ->setChildAttr(['data-parent' => 'grase_radmin_homepage', 'class' => 'nav nav-pills flex-column']); // Add Parent Menu to Html Tag

        // Create Menu Items
        $menu->addChild('nav_config_general', 1)
            ->setLabel('Advanced Settings')
            ->setRoute('grase_advanced_settings')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link'])
            ->setExtra('label_icon', 'settings_application');
            //->setRoles(['ADMIN_SETTINGS_GENERAL'])
            // Contact
        $menu->addChild('nav_config_contact', 5)
            ->setLabel('Groups')
            ->setRoute('grase_groups')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link']);
            //->setRoles(['ADMIN_SETTINGS_CONTACT'])
            // Email
        $usersMenu = $menu->addChild('nav_config_users', 10)
            ->setLabel('Users')
            ->setRoute('grase_users')
            ->setListAttr(['class' => 'nav-item'])
            ->setLinkAttr(['class' => 'nav-link'])
            //->setRoles(['ADMIN_SETTINGS_EMAIL'])
            ;
        $this->buildUserGroupsItems($usersMenu);

        return $menu;
    }

    private function buildUserGroupsItems(ItemInterface $usersMenu)
    {
        $groupRepo = $this->entityManager->getRepository(Group::class);
        $groups = $groupRepo->findAll();
        /** @var Group $group */
        foreach ($groups as $group) {

            $usersMenu->addChild('nav_config_users_' . $group->getId())
                ->setLabel($group->getName())
                ->setRoute('grase_users', ['group' => $group->getName()])
                ->setListAttr(['class' => 'nav-item'])
                ->setLinkAttr(['class' => 'nav-link'])
            ;
        }


        return $usersMenu;

    }
}