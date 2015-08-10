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

        app.vm.provision "docker" do |d|
            d.pull_images "cogniteev/echo:latest"
            d.pull_images "mariadb:latest"
            d.build_image "/var/www/container/"
        end

        app.vm.provision "setup", type:"shell" do |sh|
            sh.path = "container/setup.sh"
        end

        app.vm.provision "compose", type: "shell", run: "always" do |sh|
            sh.path = "container/compose.sh"
        end

    end

    #config.vm.define "db" do |db|
    #    db.vm.provider "docker" do |d|
    #        d.name = "db"
    #        d.image = "mariadb:latest"
    #        d.volumes = ["/var/lib/mysql"]
    #        d.env = {
    #            "MYSQL_ROOT_PASSWORD" => "root",
    #            "MYSQL_USER" => "user",
    #            "MYSQL_PASSWORD" => "user",
    #            "MYSQL_DATABASE" => "fireside"
    #        }
    #        d.expose = [3306]
    #    end
    #end

    #config.vm.define "app" do |app|
    #    app.vm.synced_folder "./", "/var/www"
    #    app.vm.provider "docker" do |d|
    #        d.name = "app"
    #        d.build_dir = "container"
    #        d.name = "fireside"
    #        d.link("db:db")
    #        d.ports = ["80:80", "443:443"]
    #    end
    #end
end
