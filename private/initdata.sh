#!/bin/bash
# To be run from private
#
# Add some users
# php app/cli.php main addUser 'Email' 'Name' #profileId 'password'
#
php app/cli.php main addUser 'michael.rynn@parracan.org' 'Michael Rynn'  Administrator '50upPlu$'
php app/cli.php main addUser 'michael.rynn.500@gmail.com' 'Zeny Entropy' Editor '50upPlu$' 

# Set ProfileID permissions
php app/cli.php init profile Administrator ALL
php app/cli.php init profile Member  myaccount comment
php app/cli.php init profile Editor myaccount blog comment
