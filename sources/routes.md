# Routes de l'application

| URL (chemin)             | Méthode HTTP | Contrôleur            | Méthode      | Titre HTML                                | Commentaire                                                       |
| ------------------------ | ------------ | --------------------- | ------------ | ----------------------------------------- | ----------------------------------------------------------------- |
| `/`                      | `GET`        | `MainController`      | `home`       | Bienvenue sur O'flix                      | Page d'accueil                                                    |
| `/movie/show/{id}`       | `GET`        | `MainController`      | `movieShow`  | Titre du film                             | Page du film dont l'id est fourni                                 |
| `/movies/list`           | `GET`        | `MainController`      | `moviesList` | Liste des films ou résultats de recherche | Page commune à la liste des films ou résultats de recherche       |
| `/api/movies`            | `GET`        | `ApiController`       | `getMovies`  | -                                         | Liste des films au format JSON                                    |
| `/favorites`             | `GET`        | `FavoritesController` | `list`       | Liste des favoris                         | Page commune à la liste des films ou résultats de recherche       |
| `/favorites/add/{id}`    | `POST`       | `FavoritesController` | `add`        | -                                         | Ajoute un film à la liste des favoris + redirection vers la liste |
| `/favorites/remove/{id}` | `POST`       | `FavoritesController` | `remove`     | -                                         | Ajoute un film à la liste des favoris + redirection vers la liste |
| `/favorites/empty`       | `POST`       | `FavoritesController` | `remove`     | -                                         | Ajoute un film à la liste des favoris + redirection vers la liste |
