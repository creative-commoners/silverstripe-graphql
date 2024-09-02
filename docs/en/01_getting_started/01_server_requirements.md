---
title: Server Requirements
icon: server
summary: What you will need to run GraphQL on a web server
---

### Filesystem permissions

During runtime, Silverstripe CMS needs read access for the webserver user to your webroot. When the GraphQL module is installed it also needs write access for the webserver user to the following locations:

- `.graphql-generated`: This directory is where your schema is
  stored once it [has been built](../getting_started/building_the_schema). Best practice
  is to create it ahead of time, but if the directory doesn't exist and your project root is writable, the GraphQL
  module will create it for you.
- `public/_graphql`: This directory is used for
  [schema introspection](../tips_and_tricks#schema-introspection). You should treat this folder
  the same way you treat the `.graphql-generated` folder.
