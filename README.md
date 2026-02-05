# Author Site Demo
 
## Create .env file
```shell
cp -n .env.example .env
```
... and update variables. 

You should also create a __separate mount/volume__ to create a new database unless you have backup/init scripts setup.

## Launch environment

Execute the docker-compose.yml file: 
```shell
docker compose up -d
```

## Install and activate plugins
```shell
docker exec -it docker-author-site-wpcli-1 bash -c " 
wp plugin delete hello akismet ; 
wp plugin install blockart-blocks --version=2.0.3 --activate
wp plugin install loco-translate --version=2.6.2 --activate
wp plugin install health-check query-monitor everest-forms --activate ;  
wp plugin activate mailhog ;
wp theme activate zakra ;
wp theme delete twentytwentythree twentytwentyfour twentytwentyfive;"
```

### WordPress
<http://localhost>

### phpMyAdmin
<http://localhost:8081>

### MailHog
<http://localhost:8025>

## WP CLI
You can run a single command:
```shell
docker exec -it docker-dock-it-wpcli-1 wp user list
```
or login via the terminal:
```shell
docker exec -it docker-dock-it-wpcli-1 bash
```
or open the WPCLI terminal in Docker Desktop

## Todo
We have received a few bug reports regarding PHP errors. We have discovered that the issue only exists in certain versions. We will use Docker and WPCLI to quickly change versions and test.
1. Test the site with different versions of PHP/WP. 
   1. Stop containers.
   2. Update .env file to use PHP-7.3 and WP-5.8
   3. If we change the WP version with Docker, we need to reset WordPress files. __MAKE SURE YOU ARE IN THE RIGHT FOLDER!!!__
      ```shell
      rm -rf wordpress/
      git checkout HEAD wordpress
      ```
      (Discuss how this would be different with separate mount points for plugins, themes, etc)
   4. Reset WordPress database (optional).
   5. Start containers and run WP CLI commands.
   6. Try PHP-8.0 and WP-6.0 (works)
   7. Update Blockart plugin (Fatal error)
   8. Check Blockart version: https://wordpress.org/plugins/blockart-blocks/#developers
   9. Try PHP 8.1 and WP-6.0 (Deprecated error)
   10. Check WP and PHP compatibility: https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/
   11. Try PHP 8.2 and WP-6.7
2. Modify some content and create new backup.

## Lab
Start Docker-WPD Final Homework
