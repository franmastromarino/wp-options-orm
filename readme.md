# WP ORM for WordPress Developers

## Description

WP ORM is a specialized Object Relational Mapper designed for WordPress, simplifying the management of entities using the `wp_options` table. It provides an efficient way to handle single and collection entities, ideal for plugin settings and simple plugin entities, without the need for custom databases.

The primary goal of WP ORM is to obviate the need for creating custom database or multiple wp_options entries reducing the complexity associated with managing such data in WordPress. 

This makes it an ideal solution for WordPress developers looking for a simplified and efficient way to handle data within plugins.

WP ORM excels in scenarios that require simple data management. It is particularly adept at managing settings and other basic entities within WordPress plugins. 

For applications that require more complex data structures or advanced features, we recommend using Symlink ORM, which is better suited for handling intricate data requirements.

In summary, WP ORM offers a developer-friendly, efficient, and streamlined approach to managing WordPress data, making it an excellent choice for developers looking to enhance their plugin development process without the added complexity of custom database management.

## Key Features

- **Simple Entity Management**: Easily create and manage single entities.
- **Collection Entity Handling**: Handle collection instances with ease.

## Installation

```
composer require franmastromarino/wp-options-orm
```

## Usage

WP ORM can be utilized to create and manage entities within WordPress. Below are examples showing its practical implementation:

### Single Entity

In this example we will share a plugin settings implementation.

```php
// Define the PluginSettings entity
namespace YourNamespace\Entities;

use QuadLayers\WP_Orm\Entity\SingleEntity;

class PluginSettings extends SingleEntity
{
    public $feature_enabled = 1;
    public $layout_type = 'grid';
    public $max_items = 10;
    public $post_type = array( 'post', 'page' );
    public $more = array(
            'test1' => true,
            'test2' => true,
            'test3' => false,
    );
    // Additional settings properties...
}
```

```php
// Model for PluginSettings
namespace YourNamespace\Models;

use YourNamespace\Entities\PluginSettings;
use QuadLayers\WP_Orm\Builder\SingleRepositoryBuilder;

class PluginSettingsModel
{
    protected $repository;

    public function __construct()
    {
        $builder = new SingleRepositoryBuilder();
        $builder->setTable('your_plugin_settings')
        ->setEntity(PluginSettings::class);

        $this->repository = $builder->getRepository();
    }

    public function getSettingsTable()
    {
        return $this->repository->getTable();
    }

    public function getSettings()
    {
        $entity = $this->repository->find();

        if ($entity) {
            return $entity->getProperties();
        } else {
            $admin = new PluginSettings();
            return $admin->getProperties();
        }
    }

    public function deleteSettings()
    {
        return $this->repository->delete();
    }

    public function saveSettings($data)
    {
        $entity = $this->repository->create($data);

        if ($entity) {
            return true;
        }
    }
    // Additional model methods...
}
```

### Collection Entity

In this example we will share a plugin items collection implementation.

```php
namespace YourNamespace\Entities;

use QuadLayers\WP_Orm\Entity\CollectionEntity;

class PluginItems extends CollectionEntity
{
    public static $primaryKey = 'item_id';
    public $item_id          = 0;
    public $feature_enabled = 1;
    public $layout_type = 'grid';
    public $max_items = 10;
    public $post_type = array( 'post', 'page' );
    public $more = array(
            'test1' => true,
            'test2' => true,
            'test3' => false,
    );
    // Additional settings properties...
}
```

```php
namespace YourNamespace\Models;

use YourNamespace\Entities\PluginItems;
use QuadLayers\WP_Orm\Builder\CollectionRepositoryBuilder;

class PluginItemsModel
{
    protected static $instance;
    protected $repository;

    private function __construct()
    {

        $builder = ( new CollectionRepositoryBuilder() )
        ->setTable('your_plugin_items')
        ->setEntity(PluginItems::class)
        ->setAutoIncrement(true);

        $this->repository = $builder->getRepository();
    }

    public function getItemsTable()
    {
        return $this->repository->getTable();
    }

    public function getItemDefaults()
    {
        $entity   = new PluginItems();
        $defaults = $entity->getDefaults();
        return $defaults;
    }

    public function getItem(int $item_id)
    {
        $entity = $this->repository->find($item_id);
        if ($entity) {
            return $entity->getProperties();
        }
    }

    public function deleteItem(int $item_id)
    {
        return $this->repository->delete($item_id);
    }

    public function updateItem(int $item_id, array $item)
    {
        $entity = $this->repository->update($item_id, $item);
        if ($entity) {
            return $entity->getProperties();
        }
    }

    public function createItem(array $item)
    {
        if (isset($item['item_id'])) {
            unset($item['item_id']);
        }

        $entity = $this->repository->create($item);

        if ($entity) {
            return $entity->getProperties();
        }
    }

    public function getAllItems()
    {
        $entities = $this->repository->findAll();
        if (! $entities) {
            return;
        }
        $actions = array();
        foreach ($entities as $entity) {
            $actions[] = $entity->getProperties();
        }
        return $actions;
    }

    public function deleteAllItems()
    {
        return $this->repository->deleteAll();
    }

    public static function instance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```
