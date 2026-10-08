<p align="center">
	<img src="images/orbis-icon-blue.svg" alt="Orbis" width="96" height="96">
</p>

<h1 align="center">Orbis</h1>

<p align="center">
	<strong>Run your business on WordPress.</strong><br>
	Projects, relations, time tracking, subscriptions and more — in one extendable, self-hosted business platform.
</p>

<p align="center">
	<a href="https://www.gnu.org/licenses/gpl-2.0.html"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue" alt="License: GPL-2.0-or-later"></a>
	<img src="https://img.shields.io/badge/PHP-%3E%3D8.0-777bb4" alt="PHP >= 8.0">
	<img src="https://img.shields.io/badge/WordPress-%3E%3D5.2-21759b" alt="WordPress >= 5.2">
	<a href="https://github.com/pronamic/wp-orbis/commits/main"><img src="https://img.shields.io/github/last-commit/pronamic/wp-orbis" alt="Last commit"></a>
</p>

---

Every business is different, so why squeeze yours into someone else's software? **Orbis** turns your WordPress site into a fully working business tool: an intranet, CRM and project management system in one. You keep your own hosting and your own data, and you add exactly the features you need with Orbis plugins.

Orbis is built and used every day by [Pronamic](https://www.pronamic.eu/) to run its own agency: projects, customers, hours, hosting subscriptions and invoices all live in Orbis.

## Table of contents

- [Why Orbis?](#why-orbis)
- [Features](#features)
- [Orbis plugins](#orbis-plugins)
- [Requirements](#requirements)
- [Installation](#installation)
- [REST API](#rest-api)
- [Development](#development)
- [Links](#links)

## Why Orbis?

- 🧩 **Modular.** Start with the core and switch on only the plugins you need: projects, timesheets, deals, subscriptions, invoices and more.
- 🏠 **Your data, your server.** Orbis runs in your own WordPress installation. No SaaS subscription, no vendor lock-in.
- 🛠️ **Built on WordPress.** Post types, comments, revisions, users, roles and the REST API — everything you already know just works.
- 🔌 **Connects to your tools.** Integrations for Slack, Twinfield, SiteGround, Openprovider and more.
- 🌍 **Translatable.** Fully translatable, with Dutch (`nl_NL`) included.
- 💚 **Free and open source.** Licensed under GPL-2.0-or-later.

## Features

The core plugin provides the foundation that all Orbis plugins build on:

- **Orbis admin menu** with a dashboard, settings, stats and a plugins page to install and activate Orbis plugins.
- **Teams** (`orbis_team`) to group users and connect them to pages, posts and projects.
- **Access control** that limits content to the members of the connected teams.
- **Roles and capabilities** such as `manage_orbis` to control who can use Orbis.
- **Email updates** on a configurable schedule, with hooks to add your own content.
- **vCards** for organizations and contacts via the `/vcard/` endpoint.
- **Currency settings** and money formatting via [`pronamic/wp-money`](https://github.com/pronamic/wp-money).
- **Autocomplete** for persons and organizations in the admin, powered by Select2.
- **Posts 2 Posts integration** to connect Orbis content to each other.

## Orbis plugins

The real power of Orbis lies in its plugins. Combine them to build the business tool that fits you.

### Relations

| Plugin | Description |
| ------ | ----------- |
| [Orbis Contacts](https://github.com/pronamic/wp-orbis-contacts) | Shared contact layer for persons and organizations: email addresses, phone numbers, social accounts and relations. |
| [Orbis Organizations](https://github.com/pronamic/wp-orbis-organizations) | Manage companies, associations, foundations and more, and link them to projects, persons and subscriptions. |
| [Orbis Persons](https://github.com/pronamic/wp-orbis-persons) | Manage persons with personal details such as birth date and gender. |

### Work

| Plugin | Description |
| ------ | ----------- |
| [Orbis Projects](https://github.com/pronamic/wp-orbis-projects) | Manage projects and connect them to organizations. |
| [Orbis Tasks](https://github.com/pronamic/wp-orbis-tasks) | Add tasks and connect them to projects. |
| [Orbis Timesheets](https://github.com/pronamic/wp-orbis-timesheets) | Track your work time and register hours on projects. |
| [Orbis Keychains](https://github.com/pronamic/wp-orbis-keychains) | Share login details with your team and log who used them, and why. |

### Sales and finance

| Plugin | Description |
| ------ | ----------- |
| [Orbis Deals](https://github.com/pronamic/wp-orbis-deals) | Keep track of deals, deal lines and their status: pending, won or lost. |
| [Orbis Subscriptions](https://github.com/pronamic/wp-orbis-subscriptions) | Manage subscription products and recurring subscriptions. |
| [Orbis Twinfield](https://github.com/pronamic/wp-orbis-twinfield) | Synchronize Orbis information to Twinfield. |

### Hosting and integrations

| Plugin | Description |
| ------ | ----------- |
| [Orbis SiteGround](https://github.com/pronamic/wp-orbis-siteground) | Compare SiteGround hosting accounts and websites against Orbis subscriptions. |
| [Orbis Openprovider](https://github.com/pronamic/wp-orbis-openprovider) | Check domain names against Orbis subscriptions. |
| [Orbis Monitoring](https://github.com/pronamic/wp-orbis-monitoring) | Monitor websites from Orbis. |
| [Orbis Slack](https://github.com/pronamic/wp-orbis-slack) | Integrate Orbis with Slack. |

### Theme

| Theme | Description |
| ----- | ----------- |
| [Orbis 5](https://github.com/pronamic/wt-orbis-5) | The front-end theme for Orbis, powered by Bootstrap 5.3. |

## Requirements

- PHP 8.0 or higher
- WordPress 5.2 or higher
- [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/)

## Installation

1. Build the plugin with `composer build` (see [Development](#development)), or download it from the [Pronamic WordPress Directory](https://wp.pronamic.directory/plugins/orbis/).
2. Upload `orbis.zip` via **Plugins → Add New → Upload Plugin** in WordPress and activate it.
3. Go to **Orbis → Plugins** to add the features you need.
4. Install the [Orbis theme](https://github.com/pronamic/wt-orbis-5) for a complete front-end experience.

## REST API

The plugin registers routes in the `orbis/v1` namespace:

| Method | Route                                     | Description                  |
| ------ | ----------------------------------------- | ---------------------------- |
| `GET`  | `/wp-json/orbis/v1/users/by?email={email}` | Get a user by email address. |
| `GET`  | `/wp-json/orbis/v1/users/{id}`             | Get a user by ID.            |

## Development

Clone the repository and install the dependencies:

```sh
git clone https://github.com/pronamic/wp-orbis.git
cd wp-orbis
composer install
npm install
```

Start a local WordPress environment with [`@wordpress/env`](https://www.npmjs.com/package/@wordpress/env). It installs Posts 2 Posts, Pronamic Client, Query Monitor and other development plugins:

```sh
npx wp-env start
```

### Composer scripts

| Command             | Description                                                     |
| ------------------- | --------------------------------------------------------------- |
| `composer phpcs`    | Check the code against the Pronamic coding standards.           |
| `composer phpcbf`   | Automatically fix coding standard violations.                   |
| `composer rector`   | Refactor the code with [Rector](https://getrector.com/).        |
| `composer build`    | Build the plugin and create a distribution archive in `build/`. |
| `composer make-pot` | Update the `.pot` and `.po` translation files.                  |
| `composer deploy`   | Deploy the plugin with [Deployer](https://deployer.org/).       |

## Links

- [Pronamic](https://www.pronamic.eu/)
- [Orbis on the Pronamic WordPress Directory](https://wp.pronamic.directory/plugins/orbis/)
- [Issues](https://github.com/pronamic/wp-orbis/issues)

---

<p align="center">
	Made with 💙 by <a href="https://www.pronamic.eu/">Pronamic</a> · GPL-2.0-or-later
</p>
