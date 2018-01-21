
# twain

A php-based forward-authenticating app for a reverse proxy

What it does:
- For an overview of what this does, see [here](https://hjbotha.github.io/twain)
- Allow access to sites based on
	- URL
	- Client IP address
	- Basic authentication
- Allow access with an emailed magic link (log in by email)

### How to deploy
Clone the project  
Copy config.php.example to config.php and edit it  
Make sure the following modules are enabled in your web server  
- php7
- php7-sqlite

Point your http server webroot at the public directory  
Go to http://your.web.server/init.php to initialise the sqlite database  
Edit the sqlite database to add users, sites and networks (IPv4 only currently)  
Configure traefik to send requests for the subdomain twain is running on to twain
Configure traefik* to use the published site as the forward authentication server  
Create a styles.css file in public and tweak the form in include/html.php to your heart's content. Some really pretty logon forms are just a search away.

Current versions of Traefik will not pass paths to the auth server. To be able to evaluate paths, Traefik must be compiled from master.

I'm not an experienced PHP developer by any means, so please feel free to submit PRs to improve things.  

### Usage:
To log in: Attempt to visit one of your subdomains. You should be asked to log in. Enter your username and password or just enter your username to authenticate by emailed magic link
To log out: Go to https://\<subdomain of twain>\/logout.php
