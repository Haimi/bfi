<?php

namespace BFI\Plugin;

use BFI\Acl\Resource;
use BFI\Acl\Role;
use BFI\Config;

/**
 * Class Translate
 * @package BFI\Plugin
 */
class Acl extends APlugin
{
    /**
     * C'tor
     * @param mixed $data
     */
    public function __construct($data)
    {
        $acl = new \BFI\Acl\Acl();

        foreach ($data->role as $role) {
            if (! empty($role['parent']) && $acl->getRole(strval($role['parent']))->getName() != 'dummy') {
                $newRole = new Role(strval($role['name']), $acl->getRole(strval($role['parent'])));
            } else {
                $newRole = new Role(strval($role['name']));
            }
            foreach ($role->allow as $allowedResources) {
                if (strval($allowedResources->controller[0]) == 'ALL') {
                    $newRole->allowAll();
                } elseif (empty($allowedResources->controller[0]->action)) {
                    $newRole->allow(new Resource(strval($allowedResources->controller)));
                } else {
                    // TODO: Implement
                }
            }
            foreach ($role->deny as $deniedResources) {
                if (strval($deniedResources->controller[0]) == 'ALL') {
                    $newRole->allowAll();
                } elseif (empty($deniedResources->controller[0]->action)) {
                    $newRole->allow(new Resource(strval($deniedResources->controller)));
                } else {
                    // TODO: Implement
                }
            }
            $acl->addRole($newRole);
            $newRole = null;
        }

        Config::set('ACL', $acl);
    }
}