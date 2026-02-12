# WordPress Docker Final — Author Site

Hi! This repo is my WordPress Development final running in Docker.  
Everything needed to launch the site is included, including a clean database snapshot with localhost URLs. It should boot up ready-to-use without extra setup.

---

## Setup

First create your environment file:

```shell
cp -n .env.example .env
```

Then start Docker:

```shell
docker compose up -d
```

On first launch, Docker automatically imports the database from `db/init`, so the site loads fully configured.

---

## Access the Site

### WordPress
<http://localhost>

### phpMyAdmin
<http://localhost:8081>

### MailHog
<http://localhost:8025>

---

## Demo Login (for grading)

```
Username: hhudgeon
Password: watterson
```

These are generic credentials since this repository is public.

---

## Theme

The site uses my custom child theme:

**astrahobbes** (child of **astra**)

WP-CLI commands if activation is needed:

```shell
docker compose exec wpcli wp theme activate astrahobbes
docker compose exec wpcli wp theme list
```

---

## Required Plugins

These plugins are active and included in the database snapshot:

- hh-books-reviews
- health-check
- loco-translate
- query-monitor

If plugins ever need to be reinstalled:

```shell
docker compose exec wpcli wp plugin install health-check --activate
docker compose exec wpcli wp plugin install loco-translate --activate
docker compose exec wpcli wp plugin install query-monitor --activate
docker compose exec wpcli wp plugin activate hh-books-reviews
```

To confirm active plugins:

```shell
docker compose exec wpcli wp plugin list --status=active
```

---

## Optional Reset Test

If you want to verify the database snapshot works from scratch:

```shell
docker compose down -v
docker compose up -d
```

WordPress should restart fully configured.

---

## Notes

- Database includes sanitized credentials
- URLs are configured for localhost
- Media uploads are included
- No manual WordPress setup required  
