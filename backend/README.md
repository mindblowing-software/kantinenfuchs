# dev server (local)

php -S 127.0.0.1:2222 -t public/ public/index.php

# generate jwt key
ssh-keygen -t rsa -b 4096 -m PEM -f jwtRS256.key
# Don't add passphrase
openssl rsa -in jwtRS256.key -pubout -outform PEM -out jwtRS256.key.pub
# alternate
# ssh-keygen -e -m PEM -f jwtRS256.key > jwtRS256.key.pub
cat jwtRS256.key
cat jwtRS256.key.pub

# convert PEM file to string using awk
awk 'NF {sub(/\r/, ""); printf "%s\\n",$0;}' cert-name.pem

# docker export
sudo docker save -o postgres12.tar postgres:12                      
sudo chown uo postgres12.tar
gzip postgres12.tar                           
scp postgres12.tar.gz uo@94.130.73.31:/home/uo

# docker import
gunzip postgres12.tar.gz 
sudo docker load -i postgres12.tar

# postgres
sudo docker exec -i opbnb-postgres-1 /bin/bash -c "PGPASSWORD=postgrespassword pg_dump --username postgres postgres" > dump.sql

sudo docker exec -i uo_postgres_1 /bin/bash -c "PGPASSWORD=postgrespassword psql --username postgres postgres" < dump.sql

# nginx
sudo nginx -t
sudo systemctl restart nginx

