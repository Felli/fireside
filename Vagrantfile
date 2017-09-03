# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

    #config.ssh.insert_key = true

    config.vm.provider "virtualbox" do |v|
        # Prevent VMs running on Ubuntu to lose internet connection
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
    end

    config.vm.define "fireside" do |app|
        app.vm.box = "ubuntu/trusty64"
        app.vm.network "private_network", ip: "192.168.33.10"
        app.vm.synced_folder ".", "/var/www",
            owner: "root", group: "root"

        app.vm.provision "vars", type:"shell", inline: <<-SHELL
            sudo echo "nameserver 8.8.8.8" > /etc/resolv.conf
        SHELL

        app.vm.provision "docker" do |d|
            d.pull_images "cogniteev/echo:latest"
            d.pull_images "mariadb:latest"
            d.build_image "/var/www/docker/"
        end

        app.vm.provision "setup", type:"shell", inline: <<-SHELL
            curl -s -L https://github.com/docker/compose/releases/download/1.3.3/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
            chmod +x /usr/local/bin/docker-compose
        SHELL

        app.vm.provision "compose", type: "shell", run: "always", inline: <<-SHELL
            cd /var/www/docker && docker-compose up -d
        SHELL

    end
end
