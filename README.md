# Mailer Bird Cli

Application to send e-mails using the terminal. 
This application was a quick solution to the problem I was having, there was some test batteries that I had to run 
and I wanted to know when they finished.
Long story short I wrote this application using 
[Symfony components](http://symfony.com/) and [PHPMailer](https://github.com/PHPMailer/PHPMailer).


## Dependencies

The application have the following system dependencies:

 * PHP Cli (php5-cli)
 * Curl (curl) [optional to install composer]

After installed php5-cli one should install composer with:

```bash

curl -sS https://getcomposer.org/installer | php
chmod +x composer.phar
sudo mv composer.phar /usr/bin/composer # Optional, install composer globally
```

Then, install the PHP dependencies running ```composer install``` on the root folder of the project.



## Configuring

This application have one configuration file in the ```config``` folder.
Create a file **email.yml** based on the sample configuration file **email.sample.yml**.



## Running/testing

To test/run the cli app just run: ```php sendmail.php notification:mailer <email> <subject> <body>```;
To see more details run the cli with ```--help``` option.


## Work In Progress

There is some things that should be interesting to add, like:

 * More email templates, switching via command line arguments.
 * Support for others e-mail providers (for now I'm using only GMail).

