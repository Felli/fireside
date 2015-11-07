## Fireside – A modern forum software

Fireside is a free, open-source forum software package powered by PHP and MySQL. It is designed to be:

 - **Fast.** Fireside's code is architectured to have little overhead and to be as efficient as possible.
 - **Simple.** All of Fireside's interfaces are designed around simplicity, ease-of-use, and speed.
 - **Powerful.** Fireside is designed to be easily extended, with support for plugins and skins.

Fireside is a fork of the esoTalk PHP software by Toby Zerner.

### Installation

Installing Fireside is super easy. In brief, simply:

- Download Fireside.
- Extract and upload the files to your PHP-enabled web server.
- Visit the location in your web browser and follow the instructions in the installer.

### Development

Fireside provides a Vagrant developer environment that uses Docker and Docker Compose behind the scenes. Additionally, Composer is used to pull in some PHP dependencies. Composer will not be required to run Fireside.

To run a local copy of Fireside, [install Vagrant](https://docs.vagrantup.com/v2/installation/index.html) and run:
```
$ vagrant up
```

This will start the virtual machine, pull or build the required Docker images, run Docker Compose to start the services, and tell Composer to update the dependencies.

To update dependencies during the development without restarting the vagrant machine, run:
```
vagrant provision --provision-with composer
```

### Issues

If you encounter any problems, please [create an issue](https://github.com/jsonnull/fireside/issues).
