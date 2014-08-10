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

php app/cli.php init profile Editor myaccount blog comment
# metatags
php app/cli.php init metatag 'description' 155 "<meta name='description' content='{}' />"
php app/cli.php init metatag 'author' 50 "<meta name='author' content='{}' />"
php app/cli.php init metatag 'keywords' 200 "<meta name='keywords' content='{}' />"
php app/cli.php init metatag 'og:title' 155 "<meta property='og:title'  content='{}' />"
php app/cli.php init metatag 'og:image' 200 "<meta property='og:image'  content='{}' />"
php app/cli.php init metatag 'og:description' 200 "<meta property='og:description'  content='{}' />"
