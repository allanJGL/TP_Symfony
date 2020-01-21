# TP_Symfony

## lancer docker 

``` docker-compose up -d --build ```

## lancer la migration de la bdd

```docker-compose exec web php bin/console make:migration```

```docker-compose exec web php bin/console doctrine:migration:migrate```
