# twain

A php-based forward-authenticating app for a reverse proxy

What it does:
- For an overview of what this does, see http://blog.mosli.net/2018/01/using-twain-for-forward-authentication.html
- Allow access to sites based on
	- URL
	- Client IP address
	- Basic authentication
- Allow access with an emailed magic link

## How to deploy
Clone the project  
Edit the config.php file
Make sure the following modules are enabled in your web server  
- php7
- php7-sqlite

Point your http server webroot at the public directory  
Go to http://your.web.server/init.php to initialise the sqlite database  
Edit the sqlite database to add users, sites and networks (IPv4 only currently)  
Configure traefik* to use the published site as the forward authentication server  
Create a styles.css file in public and tweak the form in include/html.php to your heart's content. Some really pretty logon forms are just a search away.

Current versions of Traefik will not pass paths to the auth server. To be able to evaluate paths, Traefik must be compiled from master.

I'm not an experienced PHP developer by any means, so please feel free to submit PRs to improve things.