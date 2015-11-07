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
            d.build_image "/var/www/docker/"
        end

        # Use the shell to install docker
        app.vm.provision "setup", type:"shell", inline: <<-SHELL
            curl -s -L https://github.com/docker/compose/releases/download/1.3.3/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
            chmod +x /usr/local/bin/docker-compose
        SHELL

        # Run compose every time the machine is brought up
        app.vm.provision "compose", type: "shell", run: "always", inline: <<-SHELL
            cd /var/www/docker && docker-compose up -d
        SHELL

    end
end
