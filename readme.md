# Description

The aim of the project is to create a community site where users can create tricks, exchange information about their tricks and, if they have an account, modify the content of their tricks.


# Installation

1 - Clone the repo

2 - Use the package manager [composer](https://getcomposer.org/doc/00-intro.md) to install packages.
```
composer install
```

# Configure your GMAIL SMTP

An SMTP is used for the homepage form contact and when a user send a comment it will warn you that you have to handle them through the admin panel so you have to configure it so just follow the steps below or you can follow this tutorial too if you want [How to configure SMTP GMAIL](https://www.youtube.com/watch?v=yuOK6D7deTo) :

Update the content of the following file `stmp_credentials_example.json` here how to do it :

- Log in to your account [GMAIL](https://gmail.com)
- Go to your profile && select the `Security` option`
- Select the `Two-Step Verification` && `App Passwords`
- On `Select an application` choose `Other` and write whatever you want
- Click on `Generate` && You'll get a password copy it and replace `password application` in the `smtp_credentials_example.json` file  by the generate one
- Replace `youremail@example.com` by your own gmail address
- Replace `smtp` by `smtp.gmail.com` && remove the word `example` from `smtp_credentials_example.json`


Once everything is set up go in the `env` file and insert this line below :
```
###> symfony/mailer ###
MAILER_DSN="smtp://emailname@gmail.com:applicationpassword@smtp.gmail.com:587?verify_peer=false"
###< symfony/mailer ###
```

WARNING : If you not add it you will not be able to register a user , ask a reset link etc...

# Database & User

Create a database called `snowtricks` and insert the `snowtricks.sql` file store in config folder in phpmyadmin


# PHPMyAdmin && Composer Version

### phpMyAdmin Version 5.2.1
### Composer Version 2.6.5

