![banner](public%2Fassets%2Fimg%2Fbanner%2Fbanner-homepage.jpg)
# Description

The aim of the project is to create a community site where users can create tricks, exchange information about their tricks and  modify the content of their tricks.


# Installation

1 - Clone the repo

2 - Use the package manager [composer](https://getcomposer.org/doc/00-intro.md) to install packages.
```
composer install
```

3 - When composer ask you to create "grump.yaml" file say no


# Configure your GMAIL SMTP

- Log in to your account [GMAIL](https://gmail.com)
- Go to your profile & select the `Security` option`
- Select the `Two-Step Verification` & `App Passwords`
- On `Select an application` choose `Other` and write whatever you want
- Click on `Generate` & You'll get a password copy it 


Once everything is set up go in the `env` file and insert this line below :
```
###> symfony/mailer ###
MAILER_DSN="smtp://emailname@gmail.com:applicationpassword@smtp.gmail.com:587?verify_peer=false"
###< symfony/mailer ###
```
Replace `emailname@gmail.com` by yours and do the same for `applicationpassword`.

WARNING : If you not add it you will not be able to register a user , ask a reset link etc...

# Database & User

Create a database called `snowtricks` in phpmyadmin and insert the `snowtricks.sql` file store in config folder 

# PHPMyAdmin & Composer Version

### phpMyAdmin Version 5.2.1
### Composer Version 2.6.5

