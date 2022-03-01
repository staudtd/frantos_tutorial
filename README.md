# frantos_tutorial

Verwendete Technologien php7.4, MySQL und Symfony 5

Credentials für die Datenbank sind frantos_tutorial:frantos123 und können in .env angepasst werden.

Um die Datenbank zu spawnen muss über das CLI im Projektroot

```
php ./bin/console doctrine:database:create
php ./bin/console doctrine:migrations:migrate
```

ausgeführt werden. Danach können der Testkunde und die -bestellungen generiert werden.

```
php ./bin/console install:customer
php ./bin/console install:orders
```
