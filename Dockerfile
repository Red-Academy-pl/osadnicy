# Użyj obrazu PHP z Apache
FROM php:8.0-apache

# Instalacja MariaDB i rozszerzenia mysqli
RUN apt-get update && apt-get install -y mariadb-server mariadb-client \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Kopiowanie plików aplikacji do kontenera
COPY ./osadnicy /var/www/html/

# Kopiowanie pliku SQL do inicjalizacji bazy danych
COPY ./init.sql /docker-entrypoint-initdb.d/

# Ustawianie odpowiednich uprawnień
RUN chown -R www-data:www-data /var/www/html/

# Skrypt uruchamiający zarówno MariaDB, jak i Apache
CMD mysqld_safe --skip-networking & \
    sleep 5 && \
    mysqladmin -u root password 'tajnehaslodb' && \
    mysql -u root -ptajnehaslodb -e "CREATE DATABASE osadnicy;" && \
    mysql -u root -ptajnehaslodb osadnicy < /docker-entrypoint-initdb.d/init.sql && \
    apache2-foreground

# Eksponowanie portu 80
EXPOSE 80

