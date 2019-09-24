# Plesk extension skeleton

Plesk extension skeleton is a quick-start for your new Plesk extension using UI library.

## Getting Started

Make sure your development environment has the following prerequisites installed:

* PHP 7.1+
* [Composer](https://getcomposer.org)
* [Node.js](https://nodejs.org)
* [Yarn](https://yarnpkg.com)

Create a new project by running `composer create-project plesk/ext-skeleton my-extension`.

Compile assets with `yarn build`.

At this point the extension is ready to be used.

To deploy it, create a .zip archive with contents of the `/src` directory (except `/frontend` subdirectory). You can then upload this archive via Extension Catalog in Plesk UI, or use the [command line utility](https://docs.plesk.com/en-US/onyx/extensions-guide/extensions-management-utility.73617/).

## License

This project is licensed under the Apache License, Version 2.0 - see the [LICENSE](LICENSE) file for details.
