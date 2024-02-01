# La Montacutaine

This repository contains the code for the v1 of [La Montacutaine](https://lamontacutaine.fr) backend
, as well as the v1 of its back office.

It was developed in collaboration with another junior developer, from scratch, over the course of one month.

Please note that the **fixtures** and the **.env.local** file containing sensitive data are not accessible from this git repository.

## Installation

If you wish to install this project on your machine, you will need **PHP**, **Composer**, and an **SQL** database (preferably **MariaDB**). Please provide the database connection information in the **DATABASE_URL** environment variable.

- Run the command `composer install`
- If you wish to use the **API** `php bin/console lexik:jwt:generate-keypair`
- Create the database `php bin/console d:d:c`
- Run the migration to create the tables `php bin/console d:m:m`
- Create an administrator account (via fixtures or directly in the database with the **ROLE_ADMIN**) or modify the Symfony security.yaml file to remove access restrictions.
