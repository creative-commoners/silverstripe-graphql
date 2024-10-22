---
title: What are plugins?
summary: An overview of how plugins work with the GraphQL schema
---

# Plugins

[CHILDREN asList]

## What are plugins?

Plugins are used to distribute reusable functionality across your schema. Some examples of commonly used plugins include:

- Adding versioning arguments to versioned DataObjects
- Adding a custom filter/sort arguments to `DataObject` queries
- Adding a one-off `VerisionedStage` enum to the schema
- Ensuring `Member` is in the schema
- And many more...

### Default plugins

By default, all schemas ship with some plugins installed that will benefit most use cases:

- The `DataObject` model (i.e. any `DataObject` based type) has:
  - An `inheritance` plugin that builds the interfaces, unions, and merges ancestral fields.
  - An `inheritedPlugins` plugin (a bit meta!) that merges plugins from ancestral types into descendants.
 installed).
- The `read` and `readOne` operations have:
  - A `canView` plugin for hiding records that do not pass a `canView()` check
- The `read` operation has:
  - A `paginateList` plugin for adding pagination arguments and types (e.g. `nodes`)

In addition to the above, the `default` schema specifically ships with an even richer set of default
plugins, including:

- A `versioning` plugin that adds `version` fields to the `DataObject` type (if `silverstripe/versioned` is installed)
- A `readVersion` plugin (if `silverstripe/versioned` is installed) that allows versioned operations on
`read` and `readOne` queries.
- A `filter` plugin for filtering queries (adds a `filter` argument)
- A `sort` plugin for sorting queries (adds a `sort` argument)

All of these are defined in the `modelConfig` section of the schema (see [configuring your schema](../getting_started/configuring_your_schema)).

#### Overriding default plugins

You can override default plugins generically in the `modelConfig` section.

```yml
# app/_graphql/config.yml
modelConfig:
  DataObject:
    plugins:
      inheritance: false # No `DataObject` models get this plugin unless opted into
    operations:
      read:
        plugins:
          paginateList: false # No `DataObject` models have paginated read operations unless opted into
```

You can override default plugins on your specific `DataObject` type and these changes will be inherited by descendants.

```yml
# app/_graphql/models.yml
Page:
  plugins:
    inheritance: false
App\PageType\MyCustomPage: {} # now has no inheritance plugin
```

Likewise, you can do the same for operations:

```yml
# app/_graphql/models.yml
Page:
  operations:
    read:
      plugins:
        readVersion: false
App\PageType\MyCustomPage:
  operations:
    read: true # has no readVersion plugin
```

### What plugins must do

There isn't a huge API surface to a plugin. They just have to:

- Implement at least one of several plugin interfaces
- Declare an identifier
- Apply themselves to the schema with the `apply(Schema $schema)` method
- Be registered with the [`PluginRegistry`](api:SilverStripe\GraphQL\Schema\Registry\PluginRegistry)

### Available plugin interfaces

Plugin interfaces are all found in the `SilverStripe\GraphQL\Schema\Interfaces` namespace

- [`SchemaUpdater`](api:SilverStripe\GraphQL\Schema\Interfaces\SchemaUpdater): Make a one-off, context-free update to the schema
- [`QueryPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\QueryPlugin): Update a generic query
- [`MutationPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\MutationPlugin): Update a generic mutation
- [`TypePlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\TypePlugin): Update a generic type
- [`FieldPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\FieldPlugin): Update a field on a generic type
- [`ModelQueryPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\ModelQueryPlugin): Update queries generated by a model, e.g. `readPages`
- [`ModelMutationPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\ModelMutationPlugin): Update mutations generated by a model, e.g. `createPage`
- [`ModelTypePlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\ModelTypePlugin): Update types that are generated by a model
- [`ModelFieldPlugin`](api:SilverStripe\GraphQL\Schema\Interfaces\ModelFieldPlugin): Update a field on types generated by a model

Wow, that's a lot of interfaces, right? This is owing mostly to issues around strict typing between interfaces,
and allows for a more expressive developer experience. Almost all of these interfaces have the same requirements,
just for different types. It's pretty easy to navigate if you know what you want to accomplish.

### Registering plugins

Plugins have to be registered to the `PluginRegistry` via the `Injector`.

```yml
SilverStripe\Core\Injector\Injector:
  SilverStripe\GraphQL\Schema\Registry\PluginRegistry:
    constructor:
      - 'App\GraphQL\Plugin\MyPlugin'
```

### Resolver middleware and afterware

The real power of plugins is the ability to distribute not just configuration across the schema, but
more importantly, *functionality*.

Fields have their own resolvers already, so we can't really get into those to change
their functionality without a massive hack. This is where the idea of **resolver middleware** and
**resolver afterware** comes in really useful.

**Resolver middleware** runs *before* the field's assigned resolver
**Resolver afterware** runs *after* the field's assigned resolver

Middlewares and afterwares are pretty straightforward. They get the same `$args`, `$context`, and `$info`
parameters as the assigned resolver, but the first argument, `$result` is mutated with each resolver.

### Further reading

[CHILDREN]