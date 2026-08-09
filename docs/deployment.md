# Production deployment on AlmaLinux

Production runs as one Docker Compose application on the AlmaLinux host. GitHub-hosted runners build and
test the image, publish it to GitHub Container Registry (GHCR), and pass its immutable digest to the
self-hosted production runner. The production runner does not compile application code.

## 1. Prepare AlmaLinux

Run these commands once as an administrator. They install Docker Engine from Docker's CentOS-compatible
repository, enable it at boot, and grant the GitHub Actions runner account access to the Docker socket.

```bash
sudo dnf -y install dnf-plugins-core
sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
sudo dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl enable --now docker
sudo usermod -aG docker <runner-user>
sudo install -d -m 0755 -o <runner-user> -g <runner-user> /srv/hebs
```

Replace `<runner-user>` with the Linux account that runs GitHub Actions. Restart the runner service (or
reboot once) after changing its group membership, then verify from that account:

```bash
docker version
docker compose version
docker run --rm hello-world
```

If the container is exposed directly, allow the configured HTTP port in `firewalld`. If an existing Caddy,
Nginx, or load balancer terminates TLS, set `HEBS_BIND_ADDRESS=127.0.0.1` and choose an unprivileged
`HEBS_HTTP_PORT`, such as `8080`, instead of opening a public port.

## 2. Configure the self-hosted runner

In the GitHub repository, open **Settings → Actions → Runners → New self-hosted runner**, select Linux
x64, and follow GitHub's commands using the dedicated runner account. Add the custom label
`hebs-production`. Install the runner as a service so it returns after a reboot.

Do not assign the `hebs-production` label to a general-purpose or pull-request runner. Only the protected
`main` branch deployment job is allowed to use this host.

## 3. Configure GitHub

Create a GitHub environment named `production`. Protection rules or required reviewers are recommended.
Add the following configuration:

| Type | Name | Value |
| --- | --- | --- |
| Environment secret | `PRODUCTION_ENV_FILE` | Complete Laravel production environment file |
| Repository variable | `ENABLE_PRODUCTION_DEPLOYMENT` | `true` |
| Environment or repository variable | `HEBS_DATA_DIR` | Absolute host path, for example `/srv/hebs` |
| Environment or repository variable | `HEBS_BIND_ADDRESS` | `0.0.0.0`, or `127.0.0.1` behind a proxy |
| Environment or repository variable | `HEBS_HTTP_PORT` | `80`, or the proxy's upstream port |

Start from [`.env.production.example`](../.env.production.example). Generate `APP_KEY` on a trusted
machine with `php artisan key:generate --show`; do not generate or replace it during deployment. Use a
unique `HEBS_ADMIN_PASSWORD` before the first deployment.

The workflow uses its scoped `GITHUB_TOKEN` for GHCR. If the repository uses organization-level package
restrictions, grant this repository read/write access to its container package.

## 4. Deploy

Push to `main`, then inspect **Actions → CI/CD**. A successful run performs these stages:

1. Build the Docker `test` target and execute database migrations, seeders, and PHPUnit inside it.
2. Build the production target and push both the commit tag and `latest` to GHCR.
3. Deploy the exact image digest on AlmaLinux and wait for the application health check.

Persistent state is stored under `HEBS_DATA_DIR`:

```text
/srv/hebs/
├── database/          SQLite database and journal files
├── uploads/           Public CMS uploads
├── deployment/        Compose file, deploy script, and protected environment file
├── current-image      Currently deployed immutable image reference
└── previous-image     Previous successful image reference
```

Docker Compose applies the `Z` SELinux relabel option to the two data bind mounts. Do not disable SELinux
for this deployment.

## Operations

Check the service:

```bash
export HEBS_DATA_DIR=/srv/hebs
export HEBS_ENV_FILE=/srv/hebs/deployment/.env.production
export HEBS_IMAGE="$(cat /srv/hebs/current-image)"
# Also export HEBS_BIND_ADDRESS and HEBS_HTTP_PORT here if they are not the defaults.
docker compose --project-name hebs --file /srv/hebs/deployment/compose.production.yml ps
docker compose --project-name hebs --file /srv/hebs/deployment/compose.production.yml logs --tail 100 web
```

Roll back to the previous successful application image:

```bash
export HEBS_DATA_DIR=/srv/hebs
export HEBS_ENV_FILE=/srv/hebs/deployment/.env.production
export HEBS_COMPOSE_FILE=/srv/hebs/deployment/compose.production.yml
export HEBS_IMAGE="$(cat /srv/hebs/previous-image)"
# Also export HEBS_BIND_ADDRESS and HEBS_HTTP_PORT here if they are not the defaults.
/srv/hebs/deployment/deploy.sh
```

The deploy script also attempts this rollback automatically if the new container fails its health check.
Database migrations are intentionally forward-only, so migrations must remain compatible with the prior
application image. Back up both `database/` and `uploads/` before schema-changing releases.
