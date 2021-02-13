# Not tested yet!!!

# Update
sudo apt-get update

# Install apache
sudo apt-get install apache2

# Configure Firewall
sudo ufw allow 'Apache'

# Download Pi-Stash in /var/www/html
cd /var/www/html
wget https://github.com/RickLugtigheid/Pi-Stash/archive/main.zip
unzip Pi-Stash-main.zip