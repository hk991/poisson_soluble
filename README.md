
## via Docker

Docker est utilisé pour PostreSQL, PHP, Nginx et Adminer.

Pour démarrer le projet, exécutez la commande suivante :

```shell
docker compose up -d --build
```
Le projet est disponible à l'adresse `http://localhost:8080`.

Pour stopper le projet exécuter la commande suivante :

```shell
docker compose down
```
Cela stoppera le serveur web, PHP et les containers Docker en les détruisants. Les données des bases de données sont conservées.



## Manuellement

*Prérequis*

Vous devez disposer des outils suivant installé sur votre machine :

* Symfony Cli
* PHP 8.3 en activant les extensions nécessaires (pdo pdo_mysql pdo_pgsql)
* Composer
* PostgreSQL

1. Ajouter un fichier __.env.local__ et configurer les variables __DATABASE_URL__ et __API_KEY__

2. Exécutez les commandes suivantes :

```shell
composer install
php bin/console sql-migrations:execute
php bin/console messenger:setup-transports
```

Il est possible que les certificats SSL ne soit pas installé. Exécutez les commandes suivantes :

```shell
symfony server:stop
symfony local:server:ca:install
symfony serve -d
```

Le projet est disponible à l'adresse `https://127.0.0.1:8000`.

Pour stopper le serveur, exécutez la commande suivante :

```shell
symfony server:stop
```


## Commande : `import-recipients`

Cette commande permet d'importer des destinataires à partir d’un fichier CSV contenant au minimum les colonnes `insee` et `phone`.

```shell
php bin/console import-recipients chemin/vers/fichier.csv
```


## POST /alerter

Envoie un message SMS à tous les destinataires correspondant à un code INSEE donné. Chaque SMS est mis en file d'attente via le composant messenger.

*Méthode HTTP*
POST

*URL*
`http://localhost:8080/alerter`

*Headers*
- `X-API-KEY: <API_KEY>` – Requis pour authentification.
- `Content-Type: application/json` – Obligatoire, l'endpoint rejette tout autre format.

*Payload*
```json
{
  "insee": "99999"
}
