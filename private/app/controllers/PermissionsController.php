<?php
namespace Pcan\Controllers;

use Pcan\Models\Profiles;
use Pcan\Models\Permissions;

/**
 * View and define permissions for the various profile levels.
 */
class PermissionsController extends ControllerBase
{
    public function indexAction()
    {
        return $this->defaultAction();
    }
    /**
     * View the permissions for a profile level, and change them if we have a POST.
     */
    public function defaultAction()
    {
        $this->view->setVar("hasData", false);
        
        if ($this->request->isPost() ) {
             $submit = $this->request->getPost('submit');
             $profile = Profiles::findFirstById($this->request->getPost('profileId'));
             
             if ($profile) {
                 
                if ($submit == 'Fetch')
                {
                    $this->view->setVar("hasData", true);
                }
                else if ($submit == 'Update' && $this->request->hasPost('permissions')) {
                    
                    // Deletes the current permissions      
                    $profile->getPermissions()->delete();

                    // Save the new permissions
                    foreach ($this->request->getPost('permissions') as $permission) {

                        $parts = explode('.', $permission);

                        $permission = new Permissions();
                        $permission->profilesId = $profile->id;
                        $permission->resource = $parts[0];
                        $permission->action = $parts[1];

                        $permission->save();
                    }

                    $this->flash->success('Permissions were updated with success');
                }

                // Rebuild the ACL with
                $this->acl->rebuild();

                // Pass the current permissions to the view
                $this->view->permissions = $this->acl->getPermissions($profile);
            }

            $this->view->profile = $profile;
        }
       
        // Pass all the active profiles
         $this->view->profiles = Profiles::find('active = "Y"');
    }
}
