# Laravel ADR
ADR pattern implementation for Laravel. The package provides a set of tools making it easier to implement the 
Action-Domain-Responder pattern.

---

### Installation
Install package using Composer:
```
composer require desmart/adr
```

Register the package's service provider in `config/app.php`:
```
'providers' => [
        (...)
        DeSmart\ADR\ServiceProvider::class,
    ],
```

### Usage
The main goal of this package is to make it easier to implement the ADR pattern. This means that you should be able to 
create Actions (Controllers with a single callable method, e.g. `execute()`) that return a Responder.

The Responder can digest a single Entity or a Collection of Entities and transform them into a JSON API response.

### Example
```
class ADRAction extends \DeSmart\ADR\Actions\BaseAction
{
    public function execute()
    {
        $user = new User('John', 'john@desmart.com');

        return $this->respondWith($user);
    }
}
```

### Model to Entity hydration
In order to hydrate an entity from a model (and vice versa) the package comes with a helper - `HydratesEntityTrait`.

Each Model class that uses this trait will be granted the `toEntity()` method which converts the Model to an Entity.
