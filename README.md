# Orbis

## Installation Process

###  Clone the Repository
Navigate to the WordPress plugin directory and clone the repository:
```bash
git clone git@github.com:pronamic/wp-orbis.git .wordpress/wp-content/plugins/
```

### Navigate to the Plugin Directory
Move into the newly created wp-orbis directory:

```bash
cd .wordpress/wp-content/plugins/wp-orbis
```

### Install Composer Dependencies
Install PHP dependencies using Composer:

```bash
composer install
```

### Install NPM Dependencies
Install the required Node.js packages:

```bash
npm install
```

### Run Build Task
Copy assets and run any necessary build tasks:

```bash
npm run copy
```

## Conflict

### [WP Extended Search](https://wordpress.org/plugins/wp-extended-search/)

This plugin adjusts the default WordPress behaviour for search, use [Search Everything](https://wordpress.org/plugins/search-everything/) instead.
