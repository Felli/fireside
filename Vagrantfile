# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

    config.vm.provider "virtualbox" do |v|
        # Prevent VMs running on Ubuntu to lose internet connection
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    end

    config.vm.define "fireside" do |app|
        app.vm.box = "ubuntu/trusty64"
        app.vm.network "private_network", type: "dhcp"
        app.vm.synced_folder ".", "/var/www",
            owner: "root", group: "root"

        # Docker registry sometimes gives too many redirects, this should help
        app.vm.provision "vars", type:"shell", inline: <<-SHELL
            sudo echo "nameserver 8.8.8.8" > /etc/resolv.conf
        SHELL

        # Use docker provisioner to pull the required images
        app.vm.provision "docker" do |d|
            d.pull_images "cogniteev/echo:latest"
            d.pull_images "mariadb:latest"
            d.pull_images "dreamhearth/fireside_base:1.0.0"
        end

        # Use the shell to install docker and composer
        app.vm.provision "setup", type:"shell", inline: <<-SHELL
            curl -s -L https://github.com/docker/compose/releases/download/1.3.3/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
            chmod +x /usr/local/bin/docker-compose
            mkdir -p /var/docker
            sudo tee /var/docker/docker-compose.yml <<- EOF
				data:
				  image: cogniteev/echo:latest
				  volumes:
				   - /var/www:/var/www
				   - /var/lib/mysql

				db:
				  image: mariadb:latest
				  ports:
				   - "3306:3306"
				  volumes_from:
				   - data
				  environment:
				   - MYSQL_ROOT_PASSWORD=root
				   - MYSQL_USER=user
				   - MYSQL_PASSWORD=user
				   - MYSQL_DATABASE=fireside

				web:
				  image: dreamhearth/fireside_base:1.0.0
				  links:
				   - db
				  ports:
				   - "80:80"
				   - "443:443"
				  volumes_from:
				   - data
			EOF
        SHELL

        # Run compose every time the machine is brought up
        app.vm.provision "compose", type: "shell", run: "always", inline: <<-SHELL
            cd /var/docker && docker-compose up -d
        SHELL

        # Every time the machine is brought up, install composer dependencies
        app.vm.provision "composer", type: "shell", run: "always", inline: <<-SHELL
            cd /var/docker && docker-compose run web sh -c "cd /var/www/ && composer install --no-progress"
        SHELL

    end
end
